<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Demo\Application\Command\Course\ChangeCourseCapacityCommand;
use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\Domain\Event\CourseDefinedEvent;

class CourseTest extends TestCase
{
    /** @test */
    public function create_course(): void
    {
        $play = $this->newPlay()
            ->dispatch(new DefineCourseCommand('1', 'Maths', 10))
            ->testEvents(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    CourseDefinedEvent::class => 1,
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
            ->withInitialCommands(new DefineCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new DefineCourseCommand($courseId, 'Maths', 10))
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
            ->withInitialCommands(new DefineCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new ChangeCourseCapacityCommand($courseId, -5))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
