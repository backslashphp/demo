<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Application\Command\Subscription\SubscribeStudentToCourseCommand;
use Demo\Application\Command\System\ResetCommand;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$dispatcher->dispatch(new ResetCommand());

$dispatcher->dispatch(new RegisterStudentCommand('1', 'John'));
$dispatcher->dispatch(new RegisterStudentCommand('2', 'Mary'));
$dispatcher->dispatch(new RegisterStudentCommand('3', 'Bill'));
$dispatcher->dispatch(new RegisterStudentCommand('4', 'James'));
$dispatcher->dispatch(new RegisterStudentCommand('5', 'Lucy'));
$dispatcher->dispatch(new RegisterStudentCommand('6', 'Brad'));
$dispatcher->dispatch(new RegisterStudentCommand('7', 'Kelly'));
$dispatcher->dispatch(new RegisterStudentCommand('8', 'Alice'));

$dispatcher->dispatch(new DefineCourseCommand('1', 'Algebra', 5));
$dispatcher->dispatch(new DefineCourseCommand('2', 'Biology', 4));
$dispatcher->dispatch(new DefineCourseCommand('3', 'Arts', 3));
$dispatcher->dispatch(new DefineCourseCommand('4', 'Physics', 3));
$dispatcher->dispatch(new DefineCourseCommand('5', 'Grammar', 4));

$dispatcher->dispatch(new SubscribeStudentToCourseCommand('1', '2'));
$dispatcher->dispatch(new SubscribeStudentToCourseCommand('2', '3'));
$dispatcher->dispatch(new SubscribeStudentToCourseCommand('2', '4'));
$dispatcher->dispatch(new SubscribeStudentToCourseCommand('3', '4'));

include __DIR__ . '/show.php';
