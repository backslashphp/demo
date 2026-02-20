<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Exception\CourseAtFullCapacityException;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseCannotExceedCapacityTest extends TestCase
{
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
