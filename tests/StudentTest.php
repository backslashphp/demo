<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\Scenario\PublishedEvents;
use Demo\Application\Command\Student\RegisterStudentCommand;
use Demo\Domain\Event\StudentRegisteredEvent;
use RuntimeException;

class StudentTest extends TestCase
{
    /** @test */
    public function register_student(): void
    {
        $play = $this->newPlay()
            ->dispatch(new RegisterStudentCommand('1', 'John'))
            ->testEvents(function (PublishedEvents $events): void {
                $this->assertPublishedEventsContainExactly([
                    StudentRegisteredEvent::class => 1,
                ], $events);
            });

        $this->scenario->play($play);
    }

    /** @test */
    public function student_id_must_be_unique(): void
    {
        $studentId = '1';

        $play = $this->newPlay()
            ->expectException(RuntimeException::class)
            ->withInitialCommands(new RegisterStudentCommand($studentId, 'John'))
            ->dispatch(new RegisterStudentCommand($studentId, 'John'))
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
