<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\CommandDispatcher\MiddlewareInterface;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseCreation\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseCreation\Exception\CourseNotDefinedException;
use Demo\Feature\CourseCreation\Exception\InvalidCourseIdException;
use Demo\Feature\CourseSubscription\Exception\CourseAtFullCapacityException;
use Demo\Feature\CourseSubscription\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Feature\CourseSubscription\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\StudentRegistration\Exception\StudentNotRegisteredException;
use Throwable;

class ExitOnErrorCommandDispatcherMiddleware implements MiddlewareInterface
{
    private bool $enabled = true;

    public function dispatch(object $command, DispatcherInterface $next): void
    {
        if (!$this->enabled) {
            $next->dispatch($command);
            return;
        }

        try {
            $next->dispatch($command);
        } catch (Throwable $t) {
            $message = match ($t::class) {
                CourseAtFullCapacityException::class => 'Course is at full capacity.',
                CourseIdAlreadyUsedException::class => 'ID is already used.',
                CourseNotDefinedException::class => 'Unknown course.',
                InvalidCourseCapacityException::class => 'Capacity must be an integer greater than 0.',
                InvalidCourseIdException::class => 'ID must be an integer.',
                StudentAlreadySubscribedToCourseException::class => 'Student is already subscribed to course.',
                StudentMaximumSubscriptionsReachedException::class => 'Student cannot subscribe to more than 3 courses.',
                StudentNotRegisteredException::class => 'Unknown student.',
                StudentNotSubscribedToCourseException::class => 'Student is not subscribed to course.',
                default => $t->getMessage() ?? $t::class,
            };
            echo 'ERROR: ' . $message . PHP_EOL;
            exit(-1);
        }
    }

    public function enable(bool $enable): void
    {
        $this->enabled = $enable;
    }
}
