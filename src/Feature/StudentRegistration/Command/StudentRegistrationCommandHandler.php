<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Command;

use Demo\Feature\StudentRegistration\Model\StudentRegistrationModel;
use Demo\Infrastructure\AbstractCommandHandler;

class StudentRegistrationCommandHandler extends AbstractCommandHandler
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
