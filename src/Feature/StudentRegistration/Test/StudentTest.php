<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\StudentListView\Projection\StudentListProjection;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Infrastructure\TestCase;
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
                    $this->assertContains('1', $studentList->getStudentIds());
                }),
        );
    }
}
