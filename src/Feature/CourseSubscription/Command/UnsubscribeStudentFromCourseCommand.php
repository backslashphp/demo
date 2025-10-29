<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Command;

readonly class UnsubscribeStudentFromCourseCommand
{
    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {
    }
}
