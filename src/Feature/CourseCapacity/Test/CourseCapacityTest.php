<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseCreation\Command\DefineCourseCommand;
use Demo\Feature\CourseCreation\Exception\CourseNotDefinedException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseCapacityTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function change_to_invalid_capacity(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    InvalidCourseCapacityException::class,
                )
                ->withInitialCommands(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->dispatch(
                    new ChangeCourseCapacityCommand('1', -5),
                ),
        );
    }

    #[Test]
    public function change_to_same_capacity(): void
    {
        $this->scenario->play(
            new Play()
                ->withInitialCommands(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->dispatch(
                    new ChangeCourseCapacityCommand('1', 10),
                )
                ->testEvents(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsDoNotContain(CourseCapacityChangedEvent::class, $events);
                }),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function change_capacity_of_undefined_course(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    CourseNotDefinedException::class,
                )
                ->dispatch(
                    new ChangeCourseCapacityCommand('1', 10),
                ),
        );
    }
}
