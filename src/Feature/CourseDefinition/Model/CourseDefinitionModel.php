<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Model;

use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\Query;
use Backslash\Model\AbstractModel;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseDefinition\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseDefinition\Exception\InvalidCourseIdException;

class CourseDefinitionModel extends AbstractModel
{
    private bool $courseExists = false;

    public static function buildQuery(string $courseId): Query
    {
        return new Query()
            ->withItem(
                EventClass::in(CourseDefinedEvent::class),
                Identifier::is('courseId', $courseId),
            );
    }

    public function define(string $courseId, string $name, int $capacity): void
    {
        if ($this->courseExists) {
            throw new CourseIdAlreadyUsedException();
        }
        if (!ctype_digit($courseId)) {
            throw new InvalidCourseIdException();
        }
        if ($capacity <= 0) {
            throw new InvalidCourseCapacityException();
        }
        $this->record(new CourseDefinedEvent($courseId, mb_substr($name, 0, 50), $capacity));
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->courseExists = true;
    }
}
