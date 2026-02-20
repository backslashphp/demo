<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseDefinition\Exception\CourseNotDefinedException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseMustBeDefinedToSubscribeTest extends TestCase
{
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
}
