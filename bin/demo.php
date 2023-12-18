<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Project\CreateProjectCommand;
use Demo\Application\Command\Task\CreateTaskCommand;
use Demo\Application\Command\Task\StartTaskCommand;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

include __DIR__ . '/reset.php';

$project1Id = Uuid::uuid4()->toString();
$project1Task1Id = Uuid::uuid4()->toString();
$project1Task2Id = Uuid::uuid4()->toString();
$project1Task3Id = Uuid::uuid4()->toString();

$dispatcher->dispatch(new CreateProjectCommand($project1Id, 'Build a house'));
$dispatcher->dispatch(new CreateTaskCommand($project1Task1Id, $project1Id, 'Find location'));
$dispatcher->dispatch(new CreateTaskCommand($project1Task2Id, $project1Id, 'Dig ground'));
$dispatcher->dispatch(new CreateTaskCommand($project1Task3Id, $project1Id, 'Paint walls'));

$dispatcher->dispatch(new StartTaskCommand($project1Task3Id));

$project2Id = Uuid::uuid4()->toString();
$project2Task1Id = Uuid::uuid4()->toString();
$project2Task2Id = Uuid::uuid4()->toString();

$dispatcher->dispatch(new CreateProjectCommand($project2Id, 'Write a book'));
$dispatcher->dispatch(new CreateTaskCommand($project2Task1Id, $project2Id, 'Find a good story'));
$dispatcher->dispatch(new CreateTaskCommand($project2Task2Id, $project2Id, 'Check grammar'));

echo 'DEMO PROJECTS GENERATED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/list.php';
