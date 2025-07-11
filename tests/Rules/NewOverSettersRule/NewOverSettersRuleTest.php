<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\Ctor\Collector\NewWithFollowingSettersCollector;
use TomasVotruba\Ctor\Rules\NewOverSettersRule;
use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class NewOverSettersRuleTest extends RuleTestCase
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public static function provideData(): Iterator
    {
        $errorMessage = sprintf(NewOverSettersRule::ERROR_MESSAGE, SomeObject::class, 2, 'setName', PHP_EOL);

        yield [__DIR__ . '/Fixture/AlwaysSetters.php', [[$errorMessage, -1]]];
        yield [__DIR__ . '/Fixture/AlwaysSettersWithDifferentOrder.php', [[$errorMessage, -1]]];
        yield [__DIR__ . '/Fixture/ThreeTimesAlwaysSetters.php', [[$errorMessage, -1]]];

        yield [__DIR__ . '/Fixture/SkipDifferentSingleMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipOnceThanTwiceMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnInMiddle.php', []];
        yield [__DIR__ . '/Fixture/SkipCalledOnlyOnce.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeKernel.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../../config/extension.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NewOverSettersRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(NewWithFollowingSettersCollector::class)];
    }
}
