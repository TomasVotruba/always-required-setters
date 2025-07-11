<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\Ctor\Collector\NewWithFollowingSettersCollector;
use TomasVotruba\Ctor\Enum\RuleIdentifier;

/**
 * @see NewWithFollowingSettersCollector
 * @see \TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\NewOverSettersRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class NewOverSettersRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is always created with same %d setters: "%s()"%sPass these values via constructor instead';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $collectedDataByFile = $node->get(NewWithFollowingSettersCollector::class);

        // group class + always called setters
        // if 0 setters, skipp it
        // if its always the same setters, report it

        $classesToSetterHashes = $this->groupClassesToSetterNames($collectedDataByFile);

        $ruleErrors = [];

        foreach ($classesToSetterHashes as $className => $setterNameGroups) {
            // we need at least 2 different occurrences to compare
            if (count($setterNameGroups) === 1) {
                continue;
            }

            if (! $this->areAlwaysTheSameMethodNames($setterNameGroups)) {
                continue;
            }

            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);
            $setterNameGroup = $setterNameGroups[0];

            $errorMessage = sprintf(
                self::ERROR_MESSAGE,
                $className,
                count($setterNameGroup),
                implode('()", "', $setterNameGroup),
                PHP_EOL
            );

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::NEW_OVER_SETTERS)
                ->file((string) $classReflection->getFileName())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param mixed[] $collectedDataByFile
     * @return array<string, string[][]>
     */
    private function groupClassesToSetterNames(array $collectedDataByFile): array
    {
        $classesToSetters = [];
        foreach ($collectedDataByFile as $collectedData) {

            foreach ($collectedData as $collectedItems) {
                foreach ($collectedItems as $collectedItem) {
                    if (count($collectedItem[NewWithFollowingSettersCollector::SETTER_NAMES]) === 0) {
                        continue;
                    }

                    $className = $collectedItem['className'];

                    $uniqueSetterNames = array_unique($collectedItem[NewWithFollowingSettersCollector::SETTER_NAMES]);
                    sort($uniqueSetterNames);

                    $classesToSetters[$className][] = $uniqueSetterNames;
                }
            }
        }

        return $classesToSetters;
    }

    /**
     * @param array<string[]> $methodNameGroups
     */
    private function areAlwaysTheSameMethodNames(array $methodNameGroups): bool
    {
        $methodNameHashes = [];
        foreach ($methodNameGroups as $methodNameGroup) {
            $methodNameHashes[] = implode('_', $methodNameGroup);
        }

        return count(array_unique($methodNameHashes)) === 1;
    }
}
