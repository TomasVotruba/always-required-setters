<?php

declare(strict_types=1);

namespace TomasVotruba\Ctor\Tests\Rules\NewOverSettersRule\Source;

use Symfony\Component\HttpKernel\Kernel;

final class SomeKernel extends Kernel
{
    public function setName(string $name)
    {
    }
}
