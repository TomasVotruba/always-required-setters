<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipReturnInMiddle
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');

        if (mt_rand(0, 100) > 50) {
            return;
        }

        $alwaysSetters->setAge(25);
    }

    public function second()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }
}
