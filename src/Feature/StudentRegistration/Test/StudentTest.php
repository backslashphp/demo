<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\Shared\Projection\StudentList\StudentListProjection;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Feature\StudentRegistration\Exception\InvalidStudentIdException;
use Demo\Feature\StudentRegistration\Exception\StudentIdAlreadyUsedException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentTest extends TestCase
{
    #[Test]
    public function register_student_happy_path(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->then(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsContainExactly([
                        StudentRegisteredEvent::class => 1,
                    ], $events);
                })
                ->then(function (UpdatedProjections $projections): void {
                    $this->assertUpdatedProjectionsContainExactly([
                        StudentListProjection::class => 1,
                    ], $projections);

                    /** @var StudentListProjection $studentList */
                    $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                    $this->assertStringContainsString('John', (string) $studentList);
                }),
        );
    }

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
