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
final class NewOverSettersRule implements Rule
{
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is always created with same %d setters.%sConsider passing these values via constructor instead';

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
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

        $classesToSetterHashes = $this->groupClassesToSetterHash($collectedDataByFile);

        $ruleErrors = [];

        foreach ($classesToSetterHashes as $className => $uniqueSetterHashes) {
            // we need at least 2 different hashes to compare
            if (count($uniqueSetterHashes) === 1) {
                continue;
            }

            // if all counters are the same, report it
            if (count(array_unique($uniqueSetterHashes)) !== 1) {
                continue;
            }

            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);

            $errorMessage = sprintf(self::ERROR_MESSAGE, $className, $uniqueSetterHashes[0], PHP_EOL);

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->identifier(RuleIdentifier::NEW_OVER_SETTERS)
                ->file((string) $classReflection->getFileName())
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param mixed[] $collectedDataByFile
     * @return array<string, string[]>
     */
    private function groupClassesToSetterHash(array $collectedDataByFile): array
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

                    $settersCount = count($uniqueSetterNames);

                    $countAndMethodNameHash = $settersCount . '-' . implode('-', $uniqueSetterNames);

                    $classesToSetters[$className][] = $countAndMethodNameHash;
                }
            }
        }

        return $classesToSetters;
    }
}
