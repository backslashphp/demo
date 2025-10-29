<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('student', 'course');

$studentId = $args['student'];
$courseId = $args['course'];
$dispatcher->dispatch(new SubscribeStudentToCourseCommand($studentId, $courseId));

include __DIR__ . '/show.php';
