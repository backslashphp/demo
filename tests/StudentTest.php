<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Domain\Event\StudentRegisteredEvent;
use Demo\Domain\Exception\IdAlreadyUsedException;
use Demo\Domain\Exception\InvalidIdException;
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
                    IdAlreadyUsedException::class,
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
                    InvalidIdException::class,
                )
                ->dispatch(
                    new RegisterStudentCommand('abc', 'John'),
                ),
        );
    }
}
