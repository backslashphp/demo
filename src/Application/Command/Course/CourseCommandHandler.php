<?php

declare(strict_types=1);

namespace Demo\Application\Command\Course;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\State\CourseCancelationState;
use Demo\Domain\State\CourseCapacityState;
use Demo\Domain\State\CourseCreationState;

class CourseCommandHandler extends AbstractCommandHandler
{
    public static function getHandledCommands(): array
    {
        return [
            CancelCourseCommand::class,
            ChangeCourseCapacityCommand::class,
            CreateCourseCommand::class,
        ];
    }

    protected function handleCancelCourseCommand(CancelCourseCommand $command): void
    {
        /** @var CourseCancelationState $state */
        $state = $this->getRepository()->load(
            CourseCancelationState::class,
            CourseCancelationState::getQuery($command->courseId),
        );
        $state->cancel($command->courseId);
        $this->getRepository()->store($state);
    }

    protected function handleChangeCourseCapacityCommand(ChangeCourseCapacityCommand $command): void
    {
        /** @var CourseCapacityState $state */
        $state = $this->getRepository()->load(
            CourseCapacityState::class,
            CourseCapacityState::getQuery($command->courseId),
        );
        $state->change($command->courseId, $command->capacity);
        $this->getRepository()->store($state);
    }

    protected function handleCreateCourseCommand(CreateCourseCommand $command): void
    {
        /** @var CourseCreationState $state */
        $state = $this->getRepository()->load(
            CourseCreationState::class,
            CourseCreationState::getQuery($command->courseId),
        );
        $state->create($command->courseId, $command->name, $command->capacity);
        $this->getRepository()->store($state);
    }
}
