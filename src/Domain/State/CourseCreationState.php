<?php

declare(strict_types=1);

namespace Demo\Domain\State;

use Backslash\Domain\AbstractState;
use Backslash\EventStore\Query\EventClass;
use Backslash\EventStore\Query\Identifier;
use Backslash\EventStore\Query\QueryInterface;
use Demo\Domain\Event\CourseCreatedEvent;
use RuntimeException;
use UnexpectedValueException;

class CourseCreationState extends AbstractState
{
    private bool $courseExists = false;

    public static function getQuery(string $courseId): QueryInterface
    {
        return EventClass::is(CourseCreatedEvent::class)
            ->and(
                Identifier::is('courseId', $courseId),
            );
    }

    public function create(string $courseId, string $name, int $capacity): void
    {
        if ($this->courseExists) {
            throw new RuntimeException('ID already used.');
        }
        if (!Util::isNumber($courseId)) {
            throw new UnexpectedValueException('ID must be an number.');
        }
        if ($capacity <= 0) {
            throw new UnexpectedValueException('Capacity must be greater than 0.');
        }
        $this->apply(new CourseCreatedEvent($courseId, $name, $capacity));
    }

    protected function applyCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $this->courseExists = true;
    }
}
