<?php

namespace App\Enum;

use ReflectionClass;

class CoreEnum
{
    public static function getConstants(): array
    {
        $class = new ReflectionClass(static::class);
        return $class->getConstants();
    }

    public static function getConstantValues(): array
    {
        $class = new ReflectionClass(static::class);
        return array_values($class->getConstants());
    }
}
