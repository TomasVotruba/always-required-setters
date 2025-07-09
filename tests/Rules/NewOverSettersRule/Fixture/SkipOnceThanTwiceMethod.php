<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipOnceThanTwiceMethod
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');
        $alwaysSetters->setAge(55);
    }

    public function second()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setAge(25);
    }
}
