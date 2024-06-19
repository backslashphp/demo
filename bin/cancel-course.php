<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Course\CancelCourseCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('id');

$courseId = $args['id'];
$dispatcher->dispatch(new CancelCourseCommand($courseId));

echo 'COURSE CANCELED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
