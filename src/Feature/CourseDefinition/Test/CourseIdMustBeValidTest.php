<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseDefinition\Exception\InvalidCourseIdException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseIdMustBeValidTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function invalid_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->when(
                    new DefineCourseCommand('abc', 'Maths', 10),
                )
                ->thenExpectException(
                    InvalidCourseIdException::class,
                ),
        );
    }
}
