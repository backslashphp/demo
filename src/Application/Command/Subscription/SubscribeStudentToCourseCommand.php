<?php

declare(strict_types=1);

namespace Demo\Application\Command\Subscription;

readonly class SubscribeStudentToCourseCommand
{
    public function __construct(
        public string $studentId,
        public string $courseId,
    ) {
    }
}
