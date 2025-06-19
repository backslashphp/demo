<?php

declare(strict_types=1);

namespace Demo\Test;

use Demo\Application\Command\Course\DefineCourseCommand;
use Demo\Application\Command\Subscription\SubscribeStudentToCourseCommand;

class SubscriptionTest extends TestCase
{
    /** @test */
    public function assert_student_exists(): void
    {
        $courseId = '1';
        $nonexistentStudentId = '123';

        $play = $this->newPlay()
            ->expectExceptionMessage('Student does not exist.')
            ->withInitialCommands(new DefineCourseCommand($courseId, 'Maths', 10))
            ->dispatch(new SubscribeStudentToCourseCommand($nonexistentStudentId, $courseId))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
