<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Demo\Application\Command\Course\ChangeCourseCapacityCommand;
use Demo\Application\Command\Course\CreateCourseCommand;
use Demo\Application\Command\Enrollment\EnrollStudentInCourseCommand;
use Demo\Application\Command\Enrollment\OpenEnrollmentPeriodCommand;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Domain\Event\CourseCreatedEvent;

class CourseTest extends TestCase
{
    /** @test */
    public function create_course(): void
    {
        $play = $this->newPlay()
            ->dispatch(new CreateCourseCommand('1', 'Maths', 10))
            ->testEvents(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    CourseCreatedEvent::class => 1,
                ], $events);
            });
        $this->scenario->play($play);
    }

    /** @test */
    public function course_id_must_be_unique(): void
    {
        $courseId = '1';

        $play = $this->newPlay()
            ->expectExceptionMessage('ID already used.')
            ->withInitialCommands(new CreateCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new CreateCourseCommand($courseId, 'Maths', 10))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });
        $this->scenario->play($play);
    }

    /** @test */
    public function capacity_must_be_greater_than_zero(): void
    {
        $courseId = '1';

        $play = $this->newPlay()
            ->expectExceptionMessage('Capacity must be greater than 0.')
            ->withInitialCommands(new CreateCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new ChangeCourseCapacityCommand($courseId, -5))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }

    /** @test */
    public function capacity_cannot_be_less_than_enrollment_count(): void
    {
        $play = $this->newPlay()
            ->expectExceptionMessage('Capacity must be greater or equal to the total of enrollments.')
            ->withInitialCommands(
                new RegisterStudentCommand('1', 'John'),
                new RegisterStudentCommand('2', 'Mary'),
                new CreateCourseCommand('1', 'Maths', 10),
                new OpenEnrollmentPeriodCommand(),
                new EnrollStudentInCourseCommand('1', '1'),
                new EnrollStudentInCourseCommand('2', '1'),
            )
            ->dispatch(new ChangeCourseCapacityCommand('1', 1))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
