<?php

declare(strict_types=1);

namespace Demo\Test;

use Demo\Application\Command\Course\CreateCourseCommand;
use Demo\Application\Command\Enrollment\EnrollStudentInCourseCommand;

class EnrollmentTest extends TestCase
{
    /** @test */
    public function assert_student_exists(): void
    {
        $courseId = '1';
        $nonexistentStudentId = '123';

        $play = $this->newPlay()
            ->expectExceptionMessage('Student does not exist.')
            ->withInitialCommands(new CreateCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new EnrollStudentInCourseCommand($nonexistentStudentId, $courseId))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
