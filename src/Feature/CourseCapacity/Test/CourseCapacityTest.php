<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CourseCapacityTest extends TestCase
{
    #[Test]
    public function change_to_same_capacity(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->when(
                    new ChangeCourseCapacityCommand('1', 10),
                )
                ->then(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsDoNotContain(CourseCapacityChangedEvent::class, $events);
                }),
        );
    }
}
