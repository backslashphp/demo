<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\Console\Args;
use Demo\Feature\CourseCreation\Command\DefineCourseCommand;
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

include __DIR__ . '/show.php';
