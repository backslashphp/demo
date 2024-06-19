<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Application\Command\Enrollment\OpenEnrollmentPeriodCommand;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);

$dispatcher->dispatch(new OpenEnrollmentPeriodCommand());

echo 'ENROLLMENT PERIOD OPENED' . PHP_EOL . PHP_EOL;

include __DIR__ . '/show.php';
