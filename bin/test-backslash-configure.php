<?php

declare(strict_types=1);

use Demo\Factory\Backslash;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $userApp */
$userApp = include __DIR__ . '/../bootstrap.php';

$backslash = new Backslash($userApp)
    ->withCommandHandler(
        \Demo\Application\Command\Course\CourseCommandHandler::class,
        \Demo\Application\Command\Course\ChangeCourseCapacityCommand::class,
        \Demo\Application\Command\Course\DefineCourseCommand::class,
    )
    ->withCommandHandler(
        \Demo\Application\Command\Student\StudentCommandHandler::class,
        \Demo\Application\Command\Student\RegisterStudentCommand::class,
    )
    ->withCommandHandler(
        \Demo\Application\Command\Subscription\SubscriptionCommandHandler::class,
        \Demo\Application\Command\Subscription\SubscribeStudentToCourseCommand::class,
        \Demo\Application\Command\Subscription\UnsubscribeStudentFromCourseCommand::class,
    )
    ->withCommandHandler(
        \Demo\Application\Command\System\SystemCommandHandler::class,
        \Demo\Application\Command\System\CreateDatabaseCommand::class,
        \Demo\Application\Command\System\InitializeProjectionsCommand::class,
        \Demo\Application\Command\System\PurgeEventsCommand::class,
        \Demo\Application\Command\System\PurgeProjectionsCommand::class,
        \Demo\Application\Command\System\ResetCommand::class,
    )
    ->withDispatcherMiddlewares(
        \Demo\Infrastructure\ExitOnErrorCommandDispatcherMiddleware::class,
    )
    ->withEventHandler(
        \Demo\UI\Projection\CourseList\CourseListProjector::class,
        \Demo\Domain\Event\CourseCapacityChangedEvent::class,
        \Demo\Domain\Event\CourseDefinedEvent::class,
        \Demo\Domain\Event\StudentRegisteredEvent::class,
        \Demo\Domain\Event\StudentSubscribedToCourseEvent::class,
        \Demo\Domain\Event\StudentUnsubscribedFromCourseEvent::class,
    )
    ->withEventHandler(
        \Demo\UI\Projection\StudentList\StudentListProjector::class,
        \Demo\Domain\Event\CourseDefinedEvent::class,
        \Demo\Domain\Event\StudentRegisteredEvent::class,
        \Demo\Domain\Event\StudentSubscribedToCourseEvent::class,
        \Demo\Domain\Event\StudentUnsubscribedFromCourseEvent::class,
    )
    ->configure();

$backslash->getDispatcher()->dispatch(new \Demo\Application\Command\System\ResetCommand());
