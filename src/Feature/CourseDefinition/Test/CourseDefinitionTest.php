<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseListView\Projection\CourseListProjection;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CourseDefinitionTest extends TestCase
{
    #[Test]
    public function create_course_happy_path(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->then(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsContainExactly([
                        CourseDefinedEvent::class => 1,
                    ], $events);
                })
                ->then(function (UpdatedProjections $projections): void {
                    $this->assertUpdatedProjectionsContainExactly([
                        CourseListProjection::class => 1,
                    ], $projections);

                    /** @var CourseListProjection $courseList */
                    $courseList = $projections->getAllOf(CourseListProjection::class)[0];
                    $this->assertContains('1', $courseList->getCourseIds());
                }),
        );
    }
}
