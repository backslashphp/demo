<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCreation\Command;

use Demo\Feature\CourseCreation\Model\CourseDefinitionModel;
use Demo\Infrastructure\AbstractCommandHandler;

class CourseCreationHandler extends AbstractCommandHandler
{
    protected function handleDefineCourseCommand(DefineCourseCommand $command): void
    {
        /** @var CourseDefinitionModel $model */
        $model = $this->getRepository()->loadModel(
            CourseDefinitionModel::class,
            CourseDefinitionModel::buildQuery($command->courseId),
        );
        $model->define($command->courseId, $command->name, $command->capacity);
        $this->getRepository()->storeChanges($model);
    }
}
