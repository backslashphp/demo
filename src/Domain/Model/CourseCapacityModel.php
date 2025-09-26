<?php

declare(strict_types=1);

namespace Demo\Domain\Model;

use Backslash\Model\AbstractModel;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Exception\CourseCapacityInvalidException;
use Demo\Domain\Exception\CourseNotDefinedException;

class CourseCapacityModel extends AbstractModel
{
    private int $capacity = 0;

    private bool $courseDefined = false;

    public static function buildQuery(string $courseId): QueryInterface
    {
        return EventClass::in(
            CourseCapacityChangedEvent::class,
            CourseDefinedEvent::class,
        )->and(Identifier::is('courseId', $courseId));
    }

    public function change(string $courseId, int $newCapacity): void
    {
        if (!$this->courseDefined) {
            throw new CourseNotDefinedException();
        }
        if ($newCapacity <= 0) {
            throw new CourseCapacityInvalidException();
        }
        $this->record(new CourseCapacityChangedEvent($courseId, $this->capacity, $newCapacity));
    }

    protected function applyCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->capacity = $event->new;
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->capacity = $event->capacity;
        $this->courseDefined = true;
    }
}
