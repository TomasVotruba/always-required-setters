<?php

namespace TomasVotruba\Ctor\Tests\Rules\AvoidAlwaysCalledSettersOnNewObjectRule\Source;

class SomeObject
{
    public function setName(string $name)
    {
    }

    public function setAge(int $age)
    {
    }
}
