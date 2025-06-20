<?php

declare(strict_types=1);

namespace Demo\Application\Command\Subscription;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\State\CourseSubscriptionState;

class SubscriptionCommandHandler extends AbstractCommandHandler
{
    protected function handleSubscribeStudentToCourseCommand(SubscribeStudentToCourseCommand $command): void
    {
        /** @var CourseSubscriptionState $state */
        $state = $this->getRepository()->load(
            CourseSubscriptionState::class,
            CourseSubscriptionState::getQuery($command->studentId, $command->courseId),
        );
        $state->subscribe($command->studentId, $command->courseId);
        $this->getRepository()->store($state);
    }

    protected function handleUnsubscribeStudentFromCourseCommand(UnsubscribeStudentFromCourseCommand $command): void
    {
        /** @var CourseSubscriptionState $state */
        $state = $this->getRepository()->load(
            CourseSubscriptionState::class,
            CourseSubscriptionState::getQuery($command->studentId, $command->courseId),
        );
        $state->unsubscribe($command->studentId, $command->courseId);
        $this->getRepository()->store($state);
    }
}
