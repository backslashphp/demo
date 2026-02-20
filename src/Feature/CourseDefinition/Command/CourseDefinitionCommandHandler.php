<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Command;

use Demo\Feature\CourseDefinition\Model\CourseDefinitionModel;
use Demo\Infrastructure\AbstractCommandHandler;

class CourseDefinitionCommandHandler extends AbstractCommandHandler
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
