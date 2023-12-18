<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Project\RenameProjectCommand;
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
    $projectId = $ids->resolveProjectId((int) ($argv[1] ?? 0));
} catch (IdNotFoundException) {
    echo 'PROJECT NOT FOUND' . PHP_EOL;
    exit(1);
}
$name = $argv[2] ?? '';
$dispatcher->dispatch(new RenameProjectCommand($projectId, $name));

echo 'PROJECT RENAMED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/list.php';
