<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCreation\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseCreation\Command\DefineCourseCommand;
use Demo\Feature\CourseCreation\Event\CourseDefinedEvent;
use Demo\Feature\CourseCreation\Exception\CourseIdAlreadyUsedException;
use Demo\Feature\CourseCreation\Exception\InvalidCourseIdException;
use Demo\Feature\Shared\Projection\CourseList\CourseListProjection;
use Demo\Infrastructure\TestCase;

class CourseCreationTest extends TestCase
{
    /** @test */
    public function create_course_happy_path(): void
    {
        $this->scenario->play(
            new Play()
                ->dispatch(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->testEvents(function (PublishedEvents $events): void {
                    $this->assertPublishedEventsContainExactly([
                        CourseDefinedEvent::class => 1,
                    ], $events);
                })
                ->testProjections(function (UpdatedProjections $projections): void {
                    $this->assertUpdatedProjectionsContainExactly([
                        CourseListProjection::class => 1,
                    ], $projections);

                    /** @var CourseListProjection $courseList */
                    $courseList = $projections->getAllOf(CourseListProjection::class)[0];
                    $this->assertStringContainsString('Maths', (string) $courseList);
                }),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function reuse_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    CourseIdAlreadyUsedException::class,
                )
                ->withInitialCommands(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->dispatch(
                    new DefineCourseCommand('1', 'Maths', 10),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function invalid_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->expectException(
                    InvalidCourseIdException::class,
                )
                ->dispatch(
                    new DefineCourseCommand('abc', 'Maths', 10),
                ),
        );
    }
}
