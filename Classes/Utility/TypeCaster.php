<?php

declare(strict_types=1);

namespace Leuchtfeuer\Locate\Utility;

use Exception;

class TypeCaster
{
    public static function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) || is_bool($value) || is_float($value) || is_null($value)) {
            return (int)$value;
        }

        throw new Exception(sprintf('Value of type "%s" can not be casted to integer.', gettype($value)), 1727337294);
    }

    public static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_bool($value) || is_float($value) || is_null($value)) {
            return (string)$value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string)$value;
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new Exception(sprintf('Object of type "%s" is not stringable.', get_class($value)), 1727337280);
        }

        throw new Exception(sprintf('Value of type "%s" can not be casted to string.', gettype($value)), 1727337295);
    }

    public static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_string($value) || is_float($value) || is_null($value)) {
            return (bool)$value;
        }

        throw new Exception(sprintf('Value of type "%s" can not be casted to boolean.', gettype($value)), 1727337296);
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     * @throws Exception
     */
    public static function limitToClass(mixed $value, string $className)
    {
        if ($value instanceof $className) {
            return $value;
        }

        if (gettype($value) === 'object') {
            throw new Exception(sprintf('Object of type "%s" is not instance of "%s"', get_class($value), $className), 1727337297);
        }
        throw new Exception(sprintf('Value of type "%s" is not instance of "%s"', gettype($value), $className), 1727337298);
    }

    /**
     * @return array<int|string, mixed>
     * @throws Exception
     */
    public static function limitToArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        throw new Exception(sprintf('Value of type "%s" is not an array.', gettype($value)), 1727337299);
    }
}
