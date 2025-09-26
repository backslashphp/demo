<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\CommandDispatcher\MiddlewareInterface;
use Demo\Domain\Exception\CourseAtFullCapacityException;
use Demo\Domain\Exception\CourseCapacityInvalidException;
use Demo\Domain\Exception\CourseNotDefinedException;
use Demo\Domain\Exception\IdAlreadyUsedException;
use Demo\Domain\Exception\InvalidIdException;
use Demo\Domain\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Domain\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Domain\Exception\StudentNotRegisteredException;
use Demo\Domain\Exception\StudentNotSubscribedToCourseException;
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
                CourseCapacityInvalidException::class => 'Capacity must be an integer greater than 0.',
                CourseNotDefinedException::class => 'Unknown course.',
                IdAlreadyUsedException::class => 'ID is already used.',
                InvalidIdException::class => 'ID must be an integer.',
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
