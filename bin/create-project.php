<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Project\CreateProjectCommand;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$name = $argv[1] ?? '';
$dispatcher->dispatch(new CreateProjectCommand(Uuid::uuid4()->toString(), $name));

echo 'PROJECT CREATED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/list.php';
