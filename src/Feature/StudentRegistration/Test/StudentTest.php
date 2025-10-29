<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Test;

use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseCreation\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseCreation\Exception\InvalidCourseIdException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Test\TestCase;
use Demo\UI\Projection\StudentList\StudentListProjection;

class StudentTest extends TestCase
{
    /** @test */
    public function register_student_happy_path(): void
    {
        $this->scenario->play(
            $this->newPlay()
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
            $this->newPlay()
                ->expectException(
                    CourseIdAlreadyUsedException::class,
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
            $this->newPlay()
                ->expectException(
                    InvalidCourseIdException::class,
                )
                ->dispatch(
                    new RegisterStudentCommand('abc', 'John'),
                ),
        );
    }
}
