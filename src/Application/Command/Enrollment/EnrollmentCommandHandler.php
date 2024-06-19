<?php

declare(strict_types=1);

namespace Demo\Application\Command\Enrollment;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\State\CourseEnrollmentState;
use Demo\Domain\State\EnrollmentPeriodState;

class EnrollmentCommandHandler extends AbstractCommandHandler
{
    public static function getHandledCommands(): array
    {
        return [
            WithdrawStudentFromCourseCommand::class,
            CloseEnrollmentPeriodCommand::class,
            EnrollStudentInCourseCommand::class,
            OpenEnrollmentPeriodCommand::class,
        ];
    }

    protected function handleCloseEnrollmentPeriodCommand(CloseEnrollmentPeriodCommand $command): void
    {
        /** @var EnrollmentPeriodState $state */
        $state = $this->getRepository()->load(
            EnrollmentPeriodState::class,
            EnrollmentPeriodState::getQuery(),
        );
        $state->close();
        $this->getRepository()->store($state);
    }

    protected function handleWithdrawStudentFromCourseCommand(WithdrawStudentFromCourseCommand $command): void
    {
        /** @var CourseEnrollmentState $state */
        $state = $this->getRepository()->load(
            CourseEnrollmentState::class,
            CourseEnrollmentState::getQuery($command->studentId, $command->courseId),
        );
        $state->withdraw($command->studentId, $command->courseId);
        $this->getRepository()->store($state);
    }

    protected function handleEnrollStudentInCourseCommand(EnrollStudentInCourseCommand $command): void
    {
        /** @var CourseEnrollmentState $state */
        $state = $this->getRepository()->load(
            CourseEnrollmentState::class,
            CourseEnrollmentState::getQuery($command->studentId, $command->courseId),
        );
        $state->enroll($command->studentId, $command->courseId);
        $this->getRepository()->store($state);
    }

    protected function handleOpenEnrollmentPeriodCommand(OpenEnrollmentPeriodCommand $command): void
    {
        /** @var EnrollmentPeriodState $state */
        $state = $this->getRepository()->load(
            EnrollmentPeriodState::class,
            EnrollmentPeriodState::getQuery(),
        );
        $state->open();
        $this->getRepository()->store($state);
    }
}
