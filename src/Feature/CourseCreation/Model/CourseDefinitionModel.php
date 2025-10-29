<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCreation\Model;

use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Backslash\Model\AbstractModel;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseCreation\Event\CourseDefinedEvent;
use Demo\Feature\CourseCreation\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseCreation\Exception\InvalidCourseIdException;
use Demo\Shared\Util;

class CourseDefinitionModel extends AbstractModel
{
    private bool $courseExists = false;

    public static function buildQuery(string $courseId): QueryInterface
    {
        return EventClass::is(CourseDefinedEvent::class)
            ->and(Identifier::is('courseId', $courseId));
    }

    public function define(string $courseId, string $name, int $capacity): void
    {
        if ($this->courseExists) {
            throw new CourseIdAlreadyUsedException();
        }
        if (!Util::isNumber($courseId)) {
            throw new InvalidCourseIdException();
        }
        if ($capacity <= 0) {
            throw new InvalidCourseCapacityException();
        }
        $this->record(new CourseDefinedEvent($courseId, $name, $capacity));
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->courseExists = true;
    }
}
