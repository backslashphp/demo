<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Test;

use Backslash\Scenario\Play;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Exception\StudentIdAlreadyUsedException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentIdMustBeUniqueTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function reuse_student_id(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->when(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->thenExpectException(
                    StudentIdAlreadyUsedException::class,
                ),
        );
    }
}
