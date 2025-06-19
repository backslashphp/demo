<?php

declare(strict_types=1);

namespace Demo\Application\Command\Course;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\State\CourseCapacityState;
use Demo\Domain\State\CourseDefinitionState;

class CourseCommandHandler extends AbstractCommandHandler
{
    public static function getHandledCommands(): array
    {
        return [
            ChangeCourseCapacityCommand::class,
            DefineCourseCommand::class,
        ];
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

    protected function handleDefineCourseCommand(DefineCourseCommand $command): void
    {
        /** @var CourseDefinitionState $state */
        $state = $this->getRepository()->load(
            CourseDefinitionState::class,
            CourseDefinitionState::getQuery($command->courseId),
        );
        $state->define($command->courseId, $command->name, $command->capacity);
        $this->getRepository()->store($state);
    }
}
