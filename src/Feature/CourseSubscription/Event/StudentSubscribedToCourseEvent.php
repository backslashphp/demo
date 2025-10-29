<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Event;

use Backslash\Event\EventInterface;
use Backslash\Event\Identifiers;
use Backslash\Event\ToArrayTrait;

readonly class StudentSubscribedToCourseEvent implements EventInterface
{
    use ToArrayTrait;

    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {
    }

    public function getIdentifiers(): Identifiers
    {
        return new Identifiers(
            [
            'studentId' => $this->studentId,
            'courseId' => $this->courseId,
            ],
        );
    }
}
