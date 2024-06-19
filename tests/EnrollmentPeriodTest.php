<?php

declare(strict_types=1);

namespace Demo\Test;

use Demo\Application\Command\Enrollment\CloseEnrollmentPeriodCommand;
use Demo\Application\Command\Enrollment\OpenEnrollmentPeriodCommand;

class EnrollmentPeriodTest extends TestCase
{
    /** @test */
    public function cannot_start_if_already_started(): void
    {
        $play = $this->newPlay()
            ->expectExceptionMessage('Enrollment period is already open.')
            ->withInitialCommands(new OpenEnrollmentPeriodCommand())
            ->dispatch(new OpenEnrollmentPeriodCommand())
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }

    /** @test */
    public function cannot_end_if_not_started(): void
    {
        $play = $this->newPlay()
            ->expectExceptionMessage('Enrollment period is already close.')
            ->dispatch(new CloseEnrollmentPeriodCommand())
            ->testThat(function (): void {
                $this->assertTrue(true);
            });

        $this->scenario->play($play);
    }
}
