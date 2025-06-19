<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('id', 'name', 'capacity');

$courseId = $args['id'];
$name = $args['name'];
$capacity = $args['capacity'];
$dispatcher->dispatch(new DefineCourseCommand($courseId, $name, (int) $capacity));

echo 'COURSE DEFINED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
