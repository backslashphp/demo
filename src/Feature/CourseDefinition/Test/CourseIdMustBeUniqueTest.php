<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Test;

use Backslash\Scenario\Play;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseDefinition\Exception\CourseIdAlreadyUsedException;
use Demo\Infrastructure\TestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

class CourseIdMustBeUniqueTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function reuse_course_id(): void
    {
        $this->scenario->play(
            new Play()
                ->given(
                    new CourseDefinedEvent('1', 'Maths', 10),
                )
                ->when(
                    new DefineCourseCommand('1', 'Maths', 10),
                )
                ->thenExpectException(
                    CourseIdAlreadyUsedException::class,
                ),
        );
    }
}
