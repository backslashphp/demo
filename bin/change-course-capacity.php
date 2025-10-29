<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('id', 'capacity');

$courseId = $args['id'];
$capacity = $args['capacity'];
$dispatcher->dispatch(new ChangeCourseCapacityCommand($courseId, (int) $capacity));

include __DIR__ . '/show.php';
