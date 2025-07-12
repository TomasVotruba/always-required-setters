<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeEntity;

final class SkipEntity
{
    public function first()
    {
        $alwaysSetters = new SomeEntity();
        $alwaysSetters->setName('John');
        $alwaysSetters->setAge(25);
    }

    public function second()
    {
        $alwaysSetters = new SomeEntity();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }
}
