<?php

declare(strict_types=1);

namespace Demo\UI\Projection\StudentList;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;
use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseCreation\Event\CourseDefinedEvent;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;

class StudentListProjector implements EventHandlerInterface
{
    use EventHandlerTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    protected function handleCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $list = $this->getList();
        $list->defineCourse($event->courseId, $event->name);
        $this->projections->store($list);
    }

    protected function handleStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        $list = $this->getList();
        $list->subscribe($event->studentId, $event->courseId);
        $this->projections->store($list);
    }

    protected function handleStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        $list = $this->getList();
        $list->unsubscribe($event->studentId, $event->courseId);
        $this->projections->store($list);
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $list = $this->getList();
        $list->registerStudent($event->studentId, $event->name);
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
