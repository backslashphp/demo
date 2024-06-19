<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\UI\Args;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$args = Args::get('id', 'name');

$studentId = $args['id'];
$name = $args['name'];
$dispatcher->dispatch(new RegisterStudentCommand($studentId, $name));

echo 'STUDENT REGISTERED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
