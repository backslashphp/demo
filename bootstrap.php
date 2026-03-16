<?php

declare(strict_types=1);

namespace Demo;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\Admin\Command\CreateDatabaseCommand;
use Demo\Infrastructure\Container;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
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

    $repository = RepositoryBuilder::createWithNoAdapters()
        ->addAdapter(EnvConstAdapter::class)
        ->addWriter(PutenvAdapter::class)
        ->immutable()
        ->make();
    $dotenv = Dotenv::create($repository, __DIR__);
    $dotenv->safeLoad();

    $container = new Container();

    /** @var DispatcherInterface $dispatcher */
    $dispatcher = $container->get(DispatcherInterface::class);
    $dispatcher->dispatch(new CreateDatabaseCommand());

    return $container;
})();
