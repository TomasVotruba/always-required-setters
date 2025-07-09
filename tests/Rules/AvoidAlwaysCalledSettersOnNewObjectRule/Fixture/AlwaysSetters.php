<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\AvoidAlwaysCalledSettersOnNewObjectRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\AvoidAlwaysCalledSettersOnNewObjectRule\Source\SomeObject;

final class AlwaysSetters
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
}
