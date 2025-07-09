<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipDifferentSingleMethod
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');
    }

    public function second()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setAge(25);
    }
}
