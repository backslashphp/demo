<?php

declare(strict_types=1);

namespace Demo\Feature\Shared\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseCreation\Event\CourseDefinedEvent;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Command\UnsubscribeStudentFromCourseCommand;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\Shared\Projection\CourseList\CourseListProjection;
use Demo\Feature\Shared\Projection\StudentList\StudentListProjection;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;

/**
 * This test showcases most commonly used assertions provided by Scenario
 */
class DemoTest extends TestCase
{
    public function test_demo(): void
    {
        $studentId = '1';
        $courseId = '2';

        $subscribe = new Play()
            ->given(
                new CourseDefinedEvent($courseId, 'Maths', 30),
                new RegisterStudentCommand($studentId, 'John'),
            )
            ->when(
                new SubscribeStudentToCourseCommand($studentId, $courseId),
            )
            ->when(function (): void {
                define('MY_CONSTANT', 'some-value');
            })
            ->then(function (PublishedEvents $events) use ($studentId, $courseId): void {
                $this->assertPublishedEventsCount(1, $events);
                $this->assertPublishedEventsContainOnly(StudentSubscribedToCourseEvent::class, $events);
                $this->assertPublishedEventsDoNotContain(StudentUnsubscribedFromCourseEvent::class, $events);

                /** @var StudentSubscribedToCourseEvent $event */
                $event = $events->getAllOf(StudentSubscribedToCourseEvent::class)[0]->getEvent();
                $this->assertEquals($studentId, $event->studentId);
                $this->assertEquals($courseId, $event->courseId);
            })
            ->then(function (UpdatedProjections $projections): void {
                $this->assertUpdatedProjectionsCount(2, $projections);
                $this->assertUpdatedProjectionsContainExactly([
                    CourseListProjection::class => 1,
                    StudentListProjection::class => 1,
                ], $projections);
            })
            ->then(function (): void {
                defined('MY_CONSTANT');
            });

        $unsubscribe = new Play()
            ->when(
                new UnsubscribeStudentFromCourseCommand($studentId, $courseId),
            )
            ->then(function (PublishedEvents $events) use ($studentId, $courseId): void {
                $this->assertPublishedEventsContainExactly([
                    StudentUnsubscribedFromCourseEvent::class => 1,
                ], $events);
            });

        $oops = new Play()
            ->when(
                new UnsubscribeStudentFromCourseCommand($studentId, $courseId),
            )
            ->thenExpectException(StudentNotSubscribedToCourseException::class);

        $this->scenario->play(
            $subscribe,
            $unsubscribe,
            $oops,
        );
    }
}
