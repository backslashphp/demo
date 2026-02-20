<?php

declare(strict_types=1);

namespace Demo\Feature\StudentListView\Projection;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;
use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;

class StudentListProjector implements EventHandlerInterface
{
    use EventHandlerTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $list = $this->getList();
        $list->addStudent($event->studentId);
        $this->projections->store($list);
    }

    private function getList(): StudentListProjection
    {
        try {
            /** @var StudentListProjection $p */
            $p = $this->projections->find(StudentListProjection::ID, StudentListProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new StudentListProjection();
        }
        return $p;
    }
}
