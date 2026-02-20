<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\Feature\CourseDefinition\Exception\CourseNotDefinedException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseMustBeDefinedToChangeCapacityTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function change_capacity_of_undefined_course(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new ChangeCourseCapacityCommand('1', 10),
                )
                ->thenExpectException(
                    CourseNotDefinedException::class,
                ),
        );
    }
}
