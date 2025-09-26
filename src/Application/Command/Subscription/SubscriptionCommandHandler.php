<?php

declare(strict_types=1);

namespace Demo\Application\Command\Subscription;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\Model\CourseSubscriptionModel;

class SubscriptionCommandHandler extends AbstractCommandHandler
{
    protected function handleSubscribeStudentToCourseCommand(SubscribeStudentToCourseCommand $command): void
    {
        /** @var CourseSubscriptionModel $model */
        $model = $this->getRepository()->loadModel(
            CourseSubscriptionModel::class,
            CourseSubscriptionModel::buildQuery($command->studentId, $command->courseId),
        );
        $model->subscribe($command->studentId, $command->courseId);
        $this->getRepository()->storeChanges($model);
    }

    protected function handleUnsubscribeStudentFromCourseCommand(UnsubscribeStudentFromCourseCommand $command): void
    {
        /** @var CourseSubscriptionModel $model */
        $model = $this->getRepository()->loadModel(
            CourseSubscriptionModel::class,
            CourseSubscriptionModel::buildQuery($command->studentId, $command->courseId),
        );
        $model->unsubscribe($command->studentId, $command->courseId);
        $this->getRepository()->storeChanges($model);
    }
}
