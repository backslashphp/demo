<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\StudentRegistration\Exception\StudentNotRegisteredException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class StudentMustBeRegisteredToSubscribeTest extends TestCase
{
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
}
