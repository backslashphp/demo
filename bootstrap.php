<?php

declare(strict_types=1);

namespace Demo;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\System\CreateDatabaseCommand;
use Demo\Infrastructure\ContainerSingleton;
use Psr\Container\ContainerInterface;

return (function (): ContainerInterface {
    chdir(__DIR__);

    if (!file_exists('vendor/autoload.php')) {
        fwrite(
            STDERR,
            'PHP dependencies not installed. Run "composer install" from project root directory and try again.' . PHP_EOL,
        );
        exit(1);
    }
    include_once 'vendor/autoload.php';

    $container = ContainerSingleton::get();

    /** @var DispatcherInterface $dispatcher */
    $dispatcher = $container->get(DispatcherInterface::class);
    $dispatcher->dispatch(new CreateDatabaseCommand());

    return $container;
})();
