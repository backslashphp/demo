<?php

declare(strict_types=1);

namespace Demo\Domain\Model;

use Backslash\Model\AbstractModel;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Exception\CourseCapacityInvalidException;
use Demo\Domain\Exception\IdAlreadyUsedException;
use Demo\Domain\Exception\InvalidIdException;

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
            throw new IdAlreadyUsedException();
        }
        if (!Util::isNumber($courseId)) {
            throw new InvalidIdException();
        }
        if ($capacity <= 0) {
            throw new CourseCapacityInvalidException();
        }
        $this->record(new CourseDefinedEvent($courseId, $name, $capacity));
    }

    protected function applyCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $this->courseExists = true;
    }
}
