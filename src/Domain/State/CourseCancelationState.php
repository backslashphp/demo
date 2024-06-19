<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseCanceledEvent;
use Demo\Domain\Event\CourseCreatedEvent;
use RuntimeException;

class CourseCancelationState extends AbstractState
{
    private bool $courseExists = false;

    private bool $courseCanceled = false;

    public static function getQuery(string $courseId): QueryInterface
    {
        return EventClass::in(
            CourseCanceledEvent::class,
            CourseCreatedEvent::class,
        )->and(Identifier::is('courseId', $courseId));
    }

    public function cancel(string $courseId): void
    {
        if (!$this->courseExists) {
            throw new RuntimeException('Course does not exist.');
        }
        if ($this->courseCanceled) {
            throw new RuntimeException('Course is already canceled.');
        }
        $this->apply(new CourseCanceledEvent($courseId));
    }

    protected function applyCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseExists = true;
    }

    protected function applyCourseCanceledEvent(CourseCanceledEvent $event): void
    {
        $this->courseCanceled = true;
    }
}
