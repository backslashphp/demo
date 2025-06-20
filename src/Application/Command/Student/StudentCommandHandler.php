<?php

declare(strict_types=1);

namespace Demo\Application\Command\Student;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\State\StudentRegistrationState;

class StudentCommandHandler extends AbstractCommandHandler
{
    public static function getHandledCommands(): array
    {
        return [
            RegisterStudentCommand::class,
        ];
    }

    protected function handleRegisterStudentCommand(RegisterStudentCommand $command): void
    {
        /**
 * @var StudentRegistrationState $state
*/
        $state = $this->getRepository()->load(
            StudentRegistrationState::class,
            StudentRegistrationState::getQuery($command->studentId),
        );
        $state->register($command->studentId, $command->name);
        $this->getRepository()->store($state);
    }
}
