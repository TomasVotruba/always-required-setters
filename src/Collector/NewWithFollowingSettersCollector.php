<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Collector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;

/**
 * Collect instance of `new`,
 * which is followed by a method calls on same object
 *
 * Goal is to find objects, that are created with same set of setters,
 * then pass values via constructor instead.
 */
final readonly class NewWithFollowingSettersCollector implements Collector
{
    public const SETTER_NAMES = 'setterNames';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope)
    {
        // skip tests, as they might use manual service construction, we're not interested in
        if ($this->isTestCase($scope)) {
            return null;
        }

        $newInstancesMetadata = [];

        // basically all nodes that have ->stmts inside
        if ($node instanceof ClassMethod || $node instanceof Function_ || $node instanceof If_ || $node instanceof ElseIf_) {

            foreach ((array) $node->stmts as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if ($stmt->expr instanceof Assign) {
                    $assign = $stmt->expr;
                    if ($assign->expr instanceof New_) {
                        $new = $assign->expr;
                        if ($new->class instanceof Name) {
                            $assignedVariable = $assign->var;
                            if ($assignedVariable instanceof Variable && is_string($assignedVariable->name)) {
                                $variableName = $assignedVariable->name;
                                $className = $new->class->toString();
                                if ($this->shouldSkipClass($className)) {
                                    continue;
                                }
                                $newInstancesMetadata[] = [
                                    'variableName' => $variableName,
                                    'className' => $className,
                                    self::SETTER_NAMES => [],
                                ];
                            }

                            continue;
                        }
                    }
                }

                // waiting for first item
                if ($newInstancesMetadata === []) {
                    continue;
                }

                if ($stmt->expr instanceof MethodCall) {
                    $methodCall = $stmt->expr;

                    if ($methodCall->var instanceof Variable && $methodCall->name instanceof Identifier) {
                        foreach ($newInstancesMetadata as $key => $newInstanceMetadata) {
                            if ($newInstanceMetadata['variableName'] === $methodCall->var->name) {
                                // record the method call here
                                $setterMethodName = $methodCall->name->toString();

                                // probably not a setter
                                if (str_starts_with($setterMethodName, 'get')) {
                                    continue;
                                }

                                $newInstancesMetadata[$key][self::SETTER_NAMES][] = $setterMethodName;
                            }
                        }
                    }
                }
            }
        }

        if ($newInstancesMetadata === []) {
            return null;
        }

        return $newInstancesMetadata;
    }

    private function shouldSkipClass(string $className): bool
    {
        // must be existing class
        if (! $this->reflectionProvider->hasClass($className)) {
            return true;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if ($classReflection->getFileName() === null) {
            return true;
        }

        // skip vendor classes
        return str_contains($classReflection->getFileName(), 'vendor');
    }

    private function isTestCase(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        return str_ends_with($classReflection->getName(), 'Test');
    }
}
