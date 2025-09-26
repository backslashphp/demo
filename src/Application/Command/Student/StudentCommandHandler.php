<?php

declare(strict_types=1);

namespace Demo\Application\Command\Student;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\Model\StudentRegistrationModel;

class StudentCommandHandler extends AbstractCommandHandler
{
    protected function handleRegisterStudentCommand(RegisterStudentCommand $command): void
    {
        /** @var StudentRegistrationModel $model */
        $model = $this->getRepository()->loadModel(
            StudentRegistrationModel::class,
            StudentRegistrationModel::buildQuery($command->studentId),
        );
        $model->register($command->studentId, $command->name);
        $this->getRepository()->storeChanges($model);
    }
}
