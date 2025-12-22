<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Feature\CourseCreation\Command\DefineCourseCommand;
use Demo\Feature\CourseCreation\Exception\CourseNotDefinedException;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Command\UnsubscribeStudentFromCourseCommand;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\CourseSubscription\Exception\CourseAtFullCapacityException;
use Demo\Feature\CourseSubscription\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Feature\CourseSubscription\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\Shared\Projection\StudentList\StudentListProjection;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Exception\StudentNotRegisteredException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
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
                /** @var StudentListProjection $studentList */
                $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                $this->assertStringContainsString('John (Maths)', (string) $studentList);
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
                /** @var StudentListProjection $studentList */
                $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                $this->assertStringNotContainsString('John (Maths)', (string) $studentList);
            });

        $this->scenario->play(
            $subscribe,
            $unsubscribe,
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function subscribe_an_unregistered_student(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->when(
                    new SubscribeStudentToCourseCommand('123', '1'),
                )
                ->thenExpectException(
                    StudentNotRegisteredException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function subscribe_to_undefined_course(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->when(
                    new SubscribeStudentToCourseCommand('1', '123'),
                )
                ->thenExpectException(
                    CourseNotDefinedException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function subscribe_twice(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                    new SubscribeStudentToCourseCommand('1', '123'),
                )
                ->when(
                    new SubscribeStudentToCourseCommand('1', '123'),
                )
                ->thenExpectException(
                    StudentAlreadySubscribedToCourseException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function unsubscribe_from_course_when_not_subscribed(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                )
                ->when(
                    new UnsubscribeStudentFromCourseCommand('1', '123'),
                )
                ->thenExpectException(
                    StudentNotSubscribedToCourseException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function subscribe_to_more_than_3_courses(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                    new DefineCourseCommand('234', 'French', 10),
                    new DefineCourseCommand('345', 'Geography', 10),
                    new DefineCourseCommand('456', 'Arts', 10),
                    new SubscribeStudentToCourseCommand('1', '123'),
                    new SubscribeStudentToCourseCommand('1', '234'),
                    new SubscribeStudentToCourseCommand('1', '345'),
                )
                ->when(
                    new SubscribeStudentToCourseCommand('1', '456'),
                )
                ->thenExpectException(
                    StudentMaximumSubscriptionsReachedException::class,
                ),
        );
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function exceed_course_capacity(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new DefineCourseCommand('123', 'Maths', 3),
                    new RegisterStudentCommand('1', 'John'),
                    new RegisterStudentCommand('2', 'Mary'),
                    new RegisterStudentCommand('3', 'Bill'),
                    new RegisterStudentCommand('4', 'Suzan'),
                    new SubscribeStudentToCourseCommand('1', '123'),
                    new SubscribeStudentToCourseCommand('2', '123'),
                    new SubscribeStudentToCourseCommand('3', '123'),
                )
                ->when(
                    new SubscribeStudentToCourseCommand('4', '123'),
                )
                ->thenExpectException(
                    CourseAtFullCapacityException::class,
                ),
        );
    }
}
