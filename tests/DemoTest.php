<?php

declare(strict_types=1);


use Backslash\Event\Metadata;
use Backslash\Event\RecordedEvent;
use Backslash\Event\RecordedEventStream;
use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Application\Command\Subscription\SubscribeStudentToCourseCommand;
use Demo\Application\Command\Subscription\UnsubscribeStudentFromCourseCommand;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Event\StudentSubscribedToCourseEvent;
use Demo\Domain\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Domain\Exception\StudentNotSubscribedToCourseException;
use Demo\Test\TestCase;
use Demo\UI\Projection\CourseList\CourseListProjection;
use Demo\UI\Projection\StudentList\StudentListProjection;

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
            ->withInitialEvents(
                new RecordedEventStream(
                    RecordedEvent::create(
                        new CourseDefinedEvent($courseId, 'Maths', 30),
                        new Metadata(),
                        new DateTimeImmutable(),
                    ),
                ),
            )
            ->withInitialCommands(
                new RegisterStudentCommand($studentId, 'John'),
            )
            ->dispatch(
                new SubscribeStudentToCourseCommand($studentId, $courseId),
            )
            ->doAction(function (): void {
                define('MY_CONSTANT', 'some-value');
            })
            ->testEvents(function (PublishedEvents $events) use ($studentId, $courseId): void {
                $this->assertPublishedEventsCount(1, $events);
                $this->assertPublishedEventsContainOnly(StudentSubscribedToCourseEvent::class, $events);
                $this->assertPublishedEventsDoNotContain(StudentUnsubscribedFromCourseEvent::class, $events);

                /** @var StudentSubscribedToCourseEvent $event */
                $event = $events->getAllOf(StudentSubscribedToCourseEvent::class)[0]->getEvent();
                $this->assertEquals($studentId, $event->studentId);
                $this->assertEquals($courseId, $event->courseId);
            })
            ->testProjections(function (UpdatedProjections $projections): void {
                $this->assertUpdatedProjectionsCount(2, $projections);
                $this->assertUpdatedProjectionsContainExactly([
                    CourseListProjection::class => 1,
                    StudentListProjection::class => 1,
                ], $projections);
            })
            ->testThat(function (): void {
                defined('MY_CONSTANT');
            });

        $unsubscribe = new Play()
            ->dispatch(
                new UnsubscribeStudentFromCourseCommand($studentId, $courseId),
            )
            ->testEvents(function (PublishedEvents $events) use ($studentId, $courseId): void {
                $this->assertPublishedEventsContainExactly([
                    StudentUnsubscribedFromCourseEvent::class => 1,
                ], $events);
            });

        $oops = new Play()
            ->expectException(StudentNotSubscribedToCourseException::class)
            ->dispatch(
                new UnsubscribeStudentFromCourseCommand($studentId, $courseId),
            );

        $this->scenario->play(
            $subscribe,
            $unsubscribe,
            $oops,
        );
    }
}
