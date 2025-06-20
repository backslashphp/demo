<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Backslash\Scenario\UpdatedProjections;
use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Application\Command\Subscription\SubscribeStudentToCourseCommand;
use Demo\Application\Command\Subscription\UnsubscribeStudentFromCourseCommand;
use Demo\Domain\Event\StudentSubscribedToCourseEvent;
use Demo\Domain\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Domain\Exception\CourseAtFullCapacityException;
use Demo\Domain\Exception\CourseNotDefinedException;
use Demo\Domain\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Domain\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Domain\Exception\StudentNotRegisteredException;
use Demo\Domain\Exception\StudentNotSubscribedToCourseException;
use Demo\UI\Projection\StudentList\StudentListProjection;

class SubscriptionTest extends TestCase
{
    /** @test */
    public function course_subscription_happy_path(): void
    {
        $subscribe = $this->newPlay()
            ->withInitialCommands(
                new DefineCourseCommand('123', 'Maths', 10),
                new RegisterStudentCommand('1', 'John'),
            )
            ->dispatch(
                new SubscribeStudentToCourseCommand('1', '123'),
            )
            ->testEvents(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    StudentSubscribedToCourseEvent::class => 1,
                ], $events);
            })
            ->testProjections(function (UpdatedProjections $projections): void {
                /** @var StudentListProjection $studentList */
                $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                $this->assertStringContainsString('John (Maths)', (string) $studentList);
            });

        $unsubscribe = $this->newPlay()
            ->dispatch(
                new UnsubscribeStudentFromCourseCommand('1', '123'),
            )
            ->testEvents(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    StudentUnsubscribedFromCourseEvent::class => 1,
                ], $events);
            })
            ->testProjections(function (UpdatedProjections $projections): void {
                /** @var StudentListProjection $studentList */
                $studentList = $projections->getAllOf(StudentListProjection::class)[0];
                $this->assertStringNotContainsString('John (Maths)', (string) $studentList);
            });

        $this->scenario->play(
            $subscribe,
            $unsubscribe,
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function subscribe_an_unregistered_student(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    StudentNotRegisteredException::class,
                )
                ->withInitialCommands(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->dispatch(
                    new SubscribeStudentToCourseCommand('123', '1'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function subscribe_to_undefined_course(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    CourseNotDefinedException::class,
                )
                ->withInitialCommands(
                    new RegisterStudentCommand('1', 'John'),
                )
                ->dispatch(
                    new SubscribeStudentToCourseCommand('1', '123'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function subscribe_twice(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    StudentAlreadySubscribedToCourseException::class,
                )
                ->withInitialCommands(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                    new SubscribeStudentToCourseCommand('1', '123'),
                )
                ->dispatch(
                    new SubscribeStudentToCourseCommand('1', '123'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function unsubscribe_from_course_when_not_subscribed(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    StudentNotSubscribedToCourseException::class,
                )
                ->withInitialCommands(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                )
                ->dispatch(
                    new UnsubscribeStudentFromCourseCommand('1', '123'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function subscribe_to_more_than_3_courses(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    StudentMaximumSubscriptionsReachedException::class,
                )
                ->withInitialCommands(
                    new RegisterStudentCommand('1', 'John'),
                    new DefineCourseCommand('123', 'Maths', 10),
                    new DefineCourseCommand('234', 'French', 10),
                    new DefineCourseCommand('345', 'Geography', 10),
                    new DefineCourseCommand('456', 'Arts', 10),
                    new SubscribeStudentToCourseCommand('1', '123'),
                    new SubscribeStudentToCourseCommand('1', '234'),
                    new SubscribeStudentToCourseCommand('1', '345'),
                )
                ->dispatch(
                    new SubscribeStudentToCourseCommand('1', '456'),
                ),
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function exceed_course_capacity(): void
    {
        $this->scenario->play(
            $this->newPlay()
                ->expectException(
                    CourseAtFullCapacityException::class,
                )
                ->withInitialCommands(
                    new DefineCourseCommand('123', 'Maths', 3),
                    new RegisterStudentCommand('1', 'John'),
                    new RegisterStudentCommand('2', 'Mary'),
                    new RegisterStudentCommand('3', 'Bill'),
                    new RegisterStudentCommand('4', 'Suzan'),
                    new SubscribeStudentToCourseCommand('1', '123'),
                    new SubscribeStudentToCourseCommand('2', '123'),
                    new SubscribeStudentToCourseCommand('3', '123'),
                )
                ->dispatch(
                    new SubscribeStudentToCourseCommand('4', '123'),
                ),
        );
    }
}
