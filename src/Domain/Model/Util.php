<?php

declare(strict_types=1);

namespace Demo\Domain\Model;

class Util
{
    public static function isNumber(string $value): bool
    {
        return $value === (string) abs(intval($value));
    }
}
