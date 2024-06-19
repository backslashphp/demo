<?php

declare(strict_types=1);

namespace Demo\UI\Projection\CourseList;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Event\CourseCanceledEvent;
use Demo\Domain\Event\CourseCapacityChangedEvent;
use Demo\Domain\Event\CourseCreatedEvent;
use Demo\Domain\Event\StudentWithdrawnFromCourseEvent;
use Demo\Domain\Event\StudentEnrolledInCourseEvent;
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
            CourseCanceledEvent::class,
            CourseCapacityChangedEvent::class,
            CourseCreatedEvent::class,
            StudentWithdrawnFromCourseEvent::class,
            StudentEnrolledInCourseEvent::class,
            StudentRegisteredEvent::class,
        ];
    }

    protected function handleCourseCanceledEvent(CourseCanceledEvent $event): void
    {
        $list = $this->getList();
        $list->cancelCourse($event->courseId);
        $this->projections->store($list);
    }

    protected function handleCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $list = $this->getList();
        $list->changeCapacity($event->courseId, $event->new);
        $this->projections->store($list);
    }

    protected function handleCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $list = $this->getList();
        $list->addCourse($event->courseId, $event->name, $event->capacity);
        $this->projections->store($list);
    }

    protected function handleStudentWithdrawnFromCourseEvent(StudentWithdrawnFromCourseEvent $event): void
    {
        $list = $this->getList();
        $list->withdraw($event->courseId, $event->studentId);
        $this->projections->store($list);
    }

    protected function handleStudentEnrolledInCourseEvent(StudentEnrolledInCourseEvent $event): void
    {
        $list = $this->getList();
        $list->enroll($event->courseId, $event->studentId);
        $this->projections->store($list);
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $list = $this->getList();
        $list->addStudent($event->studentId, $event->name);
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
