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

class StudentTest extends TestCase
{
    /** @test */
    public function register_student_happy_path(): void
    {
        $this->scenario->play(
            new Play()
                ->dispatch(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->testEvents(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsContainExactly([
                        StudentRegisteredEvent::class => 1,
                    ], $events);
                })
                ->testProjections(function (UpdatedProjections $projections): void {
                    $this->assertUpdatedProjectionsContainExactly([
                        StudentListProjection::class => 1,
                    ], $projections);

                    /** @var StudentListProjection $studentList */
                    $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                    $this->assertStringContainsString('John', (string) $studentList);
                }),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function reuse_student_id(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    StudentIdAlreadyUsedException::class,
                )
                ->withInitialCommands(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->dispatch(
                    new RegisterStudentCommand('1', 'John'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function invalid_student_id(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    InvalidStudentIdException::class,
                )
                ->dispatch(
                    new RegisterStudentCommand('abc', 'John'),
                ),
        );
    }
}
