<?php

declare(strict_types=1);

namespace Demo\Application\Command\Course;

readonly class ChangeCourseCapacityCommand
{
    public function __construct(
        public string $courseId,
        public int $capacity,
    ) {
    }
}
