<?php

namespace App\Trait;

/**
 * @method static array cases()
 */
trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function toFilamentEnum(): array
    {
        return array_combine(self::values(), self::values());
    }
}
