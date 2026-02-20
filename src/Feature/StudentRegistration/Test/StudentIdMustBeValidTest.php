<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Test;

use Backslash\Scenario\Play;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Exception\InvalidStudentIdException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentIdMustBeValidTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function invalid_student_id(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new RegisterStudentCommand('abc', 'John'),
                )
                ->thenExpectException(
                    InvalidStudentIdException::class,
                ),
        );
    }
}
