<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Task\RenameTaskCommand;
use Demo\Application\IdResolver\IdNotFoundException;
use Demo\Application\IdResolver\IdResolverInterface;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);
/** @var IdResolverInterface $ids */
$ids = $container->get(IdResolverInterface::class);

try {
    $taskId = $ids->resolveTaskId((int) ($argv[1] ?? 0));
} catch (IdNotFoundException) {
    echo 'TASK NOT FOUND' . PHP_EOL;
    exit(1);
}
$name = $argv[2] ?? '';
$dispatcher->dispatch(new RenameTaskCommand($taskId, $name));

echo 'TASK RENAMED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/list.php';
