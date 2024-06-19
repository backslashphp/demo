<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Course\CancelCourseCommand;
use Demo\Application\Command\Course\CreateCourseCommand;
use Demo\Application\Command\Enrollment\EnrollStudentInCourseCommand;
use Demo\Application\Command\Enrollment\OpenEnrollmentPeriodCommand;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

include __DIR__ . '/reset.php';

$dispatcher->dispatch(new RegisterStudentCommand('1', 'John'));
$dispatcher->dispatch(new RegisterStudentCommand('2', 'Mary'));
$dispatcher->dispatch(new RegisterStudentCommand('3', 'Bill'));
$dispatcher->dispatch(new RegisterStudentCommand('4', 'James'));
$dispatcher->dispatch(new RegisterStudentCommand('5', 'Lucy'));
$dispatcher->dispatch(new RegisterStudentCommand('6', 'Brad'));
$dispatcher->dispatch(new RegisterStudentCommand('7', 'Kelly'));
$dispatcher->dispatch(new RegisterStudentCommand('8', 'Alice'));

$dispatcher->dispatch(new CreateCourseCommand('1', 'Algebra', 5));
$dispatcher->dispatch(new CreateCourseCommand('2', 'Biology', 4));
$dispatcher->dispatch(new CreateCourseCommand('3', 'Arts', 3));
$dispatcher->dispatch(new CreateCourseCommand('4', 'Physics', 3));
$dispatcher->dispatch(new CreateCourseCommand('5', 'Grammar', 4));

$dispatcher->dispatch(new OpenEnrollmentPeriodCommand());

$dispatcher->dispatch(new EnrollStudentInCourseCommand('1', '2'));
$dispatcher->dispatch(new EnrollStudentInCourseCommand('2', '3'));
$dispatcher->dispatch(new EnrollStudentInCourseCommand('2', '4'));
$dispatcher->dispatch(new EnrollStudentInCourseCommand('3', '4'));

$dispatcher->dispatch(new CancelCourseCommand('4'));

echo 'DEMO DATA GENERATED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
