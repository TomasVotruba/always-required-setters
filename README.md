# Ctor

[![Downloads](https://img.shields.io/packagist/dt/tomasvotruba/ctor.svg?style=flat-square)](https://packagist.org/packages/tomasvotruba/ctor/stats)

If you can use constructor instead of setters, use it. These PHPStan rules will help you to find such cases.

<br>

## What It Does?

This tool collects instances of `new object()` followed by a series of method calls on the same object:

```php
$human = new Human();
$human->setName('Tomas');
$human->setAge(35);
```

...and suggests turning them into constructor arguments:

```php
$human = new Human('Tomas', 35);
```

### Why?

Such chained setters often indicate implicit required dependencies. By moving them to the constructor, you make the object state explicit, safer, and easier to reason about — and even easier to test.

<br>

## Installation

```bash
composer require tomasvotruba/ctor --dev
```

## Usage

Make use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer) to load the extension automatically.

Run PHPStan and it will automatically run the rules.

<br>

Happy coding!
