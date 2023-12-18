<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Task\MoveTaskCommand;
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
try {
    $projectId = $ids->resolveProjectId((int) ($argv[2] ?? 0));
} catch (IdNotFoundException) {
    echo 'PROJECT NOT FOUND' . PHP_EOL;
    exit(1);
}
$dispatcher->dispatch(new MoveTaskCommand($taskId, $projectId));

echo 'TASK MOVED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/list.php';
