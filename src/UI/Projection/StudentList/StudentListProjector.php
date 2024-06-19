<?php

declare(strict_types=1);

namespace Demo\UI\Projection\StudentList;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Event\CourseCanceledEvent;
use Demo\Domain\Event\CourseCreatedEvent;
use Demo\Domain\Event\StudentWithdrawnFromCourseEvent;
use Demo\Domain\Event\StudentEnrolledInCourseEvent;
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
            CourseCanceledEvent::class,
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

    protected function handleCourseCreatedEvent(CourseCreatedEvent $event): void
    {
        $list = $this->getList();
        $list->addCourse($event->courseId, $event->name);
        $this->projections->store($list);
    }

    protected function handleStudentWithdrawnFromCourseEvent(StudentWithdrawnFromCourseEvent $event): void
    {
        $list = $this->getList();
        $list->withdraw($event->studentId, $event->courseId);
        $this->projections->store($list);
    }

    protected function handleStudentEnrolledInCourseEvent(StudentEnrolledInCourseEvent $event): void
    {
        $list = $this->getList();
        $list->enroll($event->studentId, $event->courseId);
        $this->projections->store($list);
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $list = $this->getList();
        $list->addStudent($event->studentId, $event->name);
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
