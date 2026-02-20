<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Exception\StudentAlreadySubscribedToCourseException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentMustNotBeAlreadySubscribedTest extends TestCase
{
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
}
