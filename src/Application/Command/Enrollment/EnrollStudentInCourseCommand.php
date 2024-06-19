<?php

declare(strict_types=1);

namespace Demo\Application\Command\Enrollment;

readonly class EnrollStudentInCourseCommand
{
    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {
    }
}
