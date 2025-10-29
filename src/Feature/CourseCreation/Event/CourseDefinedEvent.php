<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCreation\Event;

use Backslash\Event\EventInterface;
use Backslash\Event\Identifiers;
use Backslash\Event\ToArrayTrait;

readonly class CourseDefinedEvent implements EventInterface
{
    use ToArrayTrait;

    public function __construct(
        public string $courseId,
        public string $name,
        public int $capacity,
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
