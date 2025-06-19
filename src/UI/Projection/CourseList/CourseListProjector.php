<?php

declare(strict_types=1);

namespace Demo\UI\Projection\CourseList;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseDefinedEvent;
use Demo\Domain\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Domain\Event\StudentSubscribedToCourseEvent;
use Demo\Domain\Event\StudentRegisteredEvent;

class CourseListProjector extends AbstractEventHandler
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CourseCapacityChangedEvent::class,
            CourseDefinedEvent::class,
            StudentRegisteredEvent::class,
            StudentSubscribedToCourseEvent::class,
            StudentUnsubscribedFromCourseEvent::class,
        ];
    }

    protected function handleCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $list = $this->getList();
        $list->changeCapacity($event->courseId, $event->new);
        $this->projections->store($list);
    }

    protected function handleCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $list = $this->getList();
        $list->defineCourse($event->courseId, $event->name, $event->capacity);
        $this->projections->store($list);
    }

    protected function handleStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        $list = $this->getList();
        $list->subscribe($event->courseId, $event->studentId);
        $this->projections->store($list);
    }

    protected function handleStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        $list = $this->getList();
        $list->unsubscribe($event->courseId, $event->studentId);
        $this->projections->store($list);
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $list = $this->getList();
        $list->registerStudent($event->studentId, $event->name);
        $this->projections->store($list);
    }

    private function getList(): CourseListProjection
    {
        try {
            /** @var CourseListProjection $p */
            $p = $this->projections->find(CourseListProjection::ID, CourseListProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new CourseListProjection();
        }
        return $p;
    }
}
