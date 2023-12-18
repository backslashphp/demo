<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Psr\Container\ContainerInterface;

class ContainerSingleton
{
    private static ?Container $container = null;

    public static function get(): ContainerInterface
    {
        if (!self::$container) {
            self::$container = new Container();
        }
        return self::$container;
    }
}
