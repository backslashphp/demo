<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseCreatedEvent;
use Demo\Domain\Event\StudentWithdrawnFromCourseEvent;
use Demo\Domain\Event\StudentEnrolledInCourseEvent;
use RuntimeException;
use UnexpectedValueException;

class CourseCapacityState extends AbstractState
{
    private array $enrollments = [];

    private int $capacity = 0;

    private bool $courseExists = false;

    public static function getQuery(string $courseId): QueryInterface
    {
        $eventForThisCourseLifecycle = EventClass::in(
            CourseCapacityChangedEvent::class,
            CourseCreatedEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        $eventForEnrollmentsInThisCourse = EventClass::in(
            StudentWithdrawnFromCourseEvent::class,
            StudentEnrolledInCourseEvent::class,
        )->and(Identifier::is('courseId', $courseId));

        return $eventForThisCourseLifecycle
            ->or($eventForEnrollmentsInThisCourse);
    }

    public function change(string $courseId, int $newCapacity): void
    {
        if (!$this->courseExists) {
            throw new RuntimeException('Course does not exist.');
        }
        if ($newCapacity <= 0) {
            throw new UnexpectedValueException('Capacity must be greater than 0.');
        }
        if ($newCapacity < count($this->enrollments)) {
            throw new UnexpectedValueException('Capacity must be greater or equal to the total of enrollments.');
        }
        $this->apply(new CourseCapacityChangedEvent($courseId, $this->capacity, $newCapacity));
    }

    protected function applyCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->capacity = $event->new;
    }

    protected function applyCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->capacity = $event->capacity;
        $this->enrollments = [];
        $this->courseExists = true;
    }

    protected function applyStudentWithdrawnFromCourseEvent(StudentWithdrawnFromCourseEvent $event): void
    {
        unset($this->enrollments[$event->studentId]);
    }

    protected function applyStudentEnrolledInCourseEvent(StudentEnrolledInCourseEvent $event): void
    {
        $this->enrollments[$event->studentId] = $event->studentId;
    }
}
