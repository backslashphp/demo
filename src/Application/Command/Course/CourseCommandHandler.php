<?php

declare(strict_types=1);

namespace Demo\Application\Command\Course;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\Model\CourseCapacityModel;
use Demo\Domain\Model\CourseDefinitionModel;

class CourseCommandHandler extends AbstractCommandHandler
{
    protected function handleChangeCourseCapacityCommand(ChangeCourseCapacityCommand $command): void
    {
        /** @var CourseCapacityModel $model */
        $model = $this->getRepository()->loadModel(
            CourseCapacityModel::class,
            CourseCapacityModel::buildQuery($command->courseId),
        );
        $model->change($command->capacity);
        $this->getRepository()->storeChanges($model);
    }

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
