<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\AvoidAlwaysCalledSettersOnNewObjectRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\AvoidAlwaysCalledSettersOnNewObjectRule\Source\SomeObject;

final class ThreeTimesAlwaysSetters
{
    public function first()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('John');
        $alwaysSetters->setAge(25);
    }

    public function second()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }

    public function thrid()
    {
        $alwaysSetters = new SomeObject();
        $alwaysSetters->setName('Doe');
        $alwaysSetters->setAge(35);
    }
}
