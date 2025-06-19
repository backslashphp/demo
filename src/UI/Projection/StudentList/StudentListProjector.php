<?php

declare(strict_types=1);

namespace Demo\UI\Projection\StudentList;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Domain\Event\StudentSubscribedToCourseEvent;
use Demo\Domain\Event\StudentRegisteredEvent;

class StudentListProjector extends AbstractEventHandler
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CourseDefinedEvent::class,
            StudentRegisteredEvent::class,
            StudentSubscribedToCourseEvent::class,
            StudentUnsubscribedFromCourseEvent::class,
        ];
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
