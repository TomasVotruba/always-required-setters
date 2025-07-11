<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Fixture;

use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeKernel;
use TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source\SomeObject;

final class SkipSomeKernel
{
    public function first()
    {
        $someKernel = new SomeKernel();
        $someKernel->setName('John');
    }

    public function second()
    {
        $someKernel = new SomeKernel();
        $someKernel->setName('John');
    }
}
