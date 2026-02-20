<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Command\UnsubscribeStudentFromCourseCommand;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\StudentView\Projection\StudentProjection;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SubscriptionTest extends TestCase
{
    #[Test]
    public function course_subscription_happy_path(): void
    {
        $subscribe = new Play()
            ->given(
                new DefineCourseCommand('123', 'Maths', 10),
                new RegisterStudentCommand('1', 'John'),
            )
            ->when(
                new SubscribeStudentToCourseCommand('1', '123'),
            )
            ->then(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    StudentSubscribedToCourseEvent::class => 1,
                ], $events);
            })
            ->then(function (UpdatedProjections $projections): void {
                /** @var StudentProjection $student */
                $student = $projections->getAllOf(StudentProjection::class)[0];
                $enrollments = $student->jsonSerialize()['enrollments'];
                $this->assertCount(1, $enrollments);
                $this->assertSame('Maths', $enrollments[0]['courseName']);
            });

        $unsubscribe = new Play()
            ->when(
                new UnsubscribeStudentFromCourseCommand('1', '123'),
            )
            ->then(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    StudentUnsubscribedFromCourseEvent::class => 1,
                ], $events);
            })
            ->then(function (UpdatedProjections $projections): void {
                /** @var StudentProjection $student */
                $student = $projections->getAllOf(StudentProjection::class)[0];
                $this->assertCount(0, $student->jsonSerialize()['enrollments']);
            });

        $this->scenario->play(
            $subscribe,
            $unsubscribe,
        );
    }
}
