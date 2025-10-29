<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Event;

use Backslash\Event\EventInterface;
use Backslash\Event\Identifiers;
use Backslash\Event\ToArrayTrait;

readonly class CourseCapacityChangedEvent implements EventInterface
{
    use ToArrayTrait;

    public function __construct(
        public string $courseId,
        public int $previous,
        public int $new,
    ) {
    }

    public function getIdentifiers(): Identifiers
    {
        return new Identifiers(
            [
            'courseId' => $this->courseId,
            ],
        );
    }
}
