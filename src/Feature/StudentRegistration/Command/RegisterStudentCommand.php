<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Command;

readonly class RegisterStudentCommand
{
    public function __construct(
        public string $studentId,
        public string $name,
    ) {
    }
}
