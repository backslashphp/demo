<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Enrollment\WithdrawStudentFromCourseCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('student', 'course');

$studentId = $args['student'];
$courseId = $args['course'];
$dispatcher->dispatch(new WithdrawStudentFromCourseCommand($studentId, $courseId));

echo 'STUDENT DISENROLLED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
