<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Command;

readonly class DefineCourseCommand
{
    public function __construct(
        public string $courseId,
        public string $name,
        public int $capacity,
    ) {
    }
}
