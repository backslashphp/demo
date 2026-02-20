<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\UnsubscribeStudentFromCourseCommand;
use Demo\Feature\CourseSubscription\Exception\StudentNotSubscribedToCourseException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentMustBeSubscribedToUnsubscribeTest extends TestCase
{
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
}
