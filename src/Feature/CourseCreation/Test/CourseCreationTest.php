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
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseCreationTest extends TestCase
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
                    $this->assertStringContainsString('Maths', (string) $courseList);
                }),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function reuse_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new CourseDefinedEvent('1', 'Maths', 10),
                )
                ->when(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->thenExpectException(
                    CourseIdAlreadyUsedException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function invalid_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new DefineCourseCommand('abc', 'Maths', 10),
                )
                ->thenExpectException(
                    InvalidCourseIdException::class,
                ),
        );
    }
}
