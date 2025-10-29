<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Command;

readonly class SubscribeStudentToCourseCommand
{
    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {
    }
}
