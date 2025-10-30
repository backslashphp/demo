<?php

declare(strict_types=1);

namespace Demo\Feature\CourseCapacity\Command;

use Demo\Feature\CourseCapacity\Model\CourseCapacityModel;
use Demo\Feature\Shared\Command\AbstractCommandHandler;

class CourseCapacityHandler extends AbstractCommandHandler
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
}
