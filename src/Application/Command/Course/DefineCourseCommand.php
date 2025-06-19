<?php

declare(strict_types=1);

namespace Demo\Application\Command\Course;

readonly class DefineCourseCommand
{
    public function __construct(
        public string $courseId,
        public string $name,
        public int $capacity,
    ) {
    }
}
