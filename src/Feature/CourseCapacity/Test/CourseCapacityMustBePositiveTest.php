<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\Feature\CourseCapacity\Exception\InvalidCourseCapacityException;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseCapacityMustBePositiveTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function change_to_invalid_capacity(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->when(
                    new ChangeCourseCapacityCommand('1', -5),
                )
                ->thenExpectException(
                    InvalidCourseCapacityException::class,
                ),
        );
    }
}
