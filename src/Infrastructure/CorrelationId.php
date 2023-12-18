<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Ramsey\Uuid\Uuid;

class CorrelationId
{
    private static ?string $id = null;

    public static function get(): string
    {
        if (!self::$id) {
            self::$id = Uuid::uuid4()->toString();
        }
        return self::$id;
    }
}
