<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Application\Command\Course\ChangeCourseCapacityCommand;
use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Exception\CourseCapacityInvalidException;
use Demo\Domain\Exception\CourseNotDefinedException;
use Demo\Domain\Exception\IdAlreadyUsedException;
use Demo\Domain\Exception\InvalidIdException;
use Demo\UI\Projection\CourseList\CourseListProjection;

class CourseTest extends TestCase
{
    /** @test */
    public function create_course_happy_path(): void
    {
        $this->scenario->play(
            $this->newPlay()
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
            $this->newPlay()
                ->expectException(
                    IdAlreadyUsedException::class,
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
            $this->newPlay()
                ->expectException(
                    InvalidIdException::class,
                )
                ->dispatch(
                    new DefineCourseCommand('abc', 'Maths', 10),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function change_to_invalid_capacity(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    CourseCapacityInvalidException::class,
                )
                ->withInitialCommands(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->dispatch(
                    new ChangeCourseCapacityCommand('1', -5),
                ),
        );
    }

    /** @test */
    public function change_to_same_capacity(): void
    {
        $this->scenario->play(
            $this->newPlay()
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

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function change_capacity_of_undefined_course(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    CourseNotDefinedException::class,
                )
                ->dispatch(
                    new ChangeCourseCapacityCommand('1', 10),
                ),
        );
    }
}
