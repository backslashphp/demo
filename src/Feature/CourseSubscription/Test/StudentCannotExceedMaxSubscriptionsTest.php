<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Exception\StudentMaximumSubscriptionsReachedException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentCannotExceedMaxSubscriptionsTest extends TestCase
{
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
}
