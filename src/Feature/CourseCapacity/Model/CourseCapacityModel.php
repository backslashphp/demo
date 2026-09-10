<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Model;

use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\Query;
use Backslash\Model\AbstractModel;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseDefinition\Exception\CourseNotDefinedException;

class CourseCapacityModel extends AbstractModel
{
    private ?string $courseId = null;

    private int $capacity = 0;

    public static function buildQuery(string $courseId): Query
    {
        return new Query()
            ->withItem(
                EventClass::in(
                    CourseCapacityChangedEvent::class,
                    CourseDefinedEvent::class,
                ),
                Identifier::is('courseId', $courseId),
            );
    }

    public function change(int $newCapacity): void
    {
        if (is_null($this->courseId)) {
            throw new CourseNotDefinedException();
        }
        if ($newCapacity <= 0) {
            throw new InvalidCourseCapacityException();
        }
        if ($newCapacity === $this->capacity) {
            return;
        }
        $this->record(new CourseCapacityChangedEvent($this->courseId, $this->capacity, $newCapacity));
    }

    protected function applyCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $this->capacity = $event->new;
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->courseId = $event->courseId;
        $this->capacity = $event->capacity;
    }
}
