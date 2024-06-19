<?php

declare(strict_types=1);

namespace Demo\Application\Command\Student;

readonly class RegisterStudentCommand
{
    public function __construct(
        public string $studentId,
        public string $name,
    ) {
    }
}
