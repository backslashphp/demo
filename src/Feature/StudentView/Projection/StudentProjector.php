<?php

declare(strict_types=1);

namespace Demo\Feature\StudentView\Projection;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;

class StudentProjector implements EventHandlerInterface
{
    use EventHandlerTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    protected function handleCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $projection = new CourseInternalProjection($event->courseId, $event->name);
        $this->projections->store($projection);
    }

    protected function handleStudentRegisteredEvent(StudentRegisteredEvent $event): void
    {
        $projection = new StudentProjection($event->studentId, $event->name);
        $this->projections->store($projection);
    }

    protected function handleStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        $student = $this->findStudent($event->studentId);
        $course = $this->findCourse($event->courseId);
        $student->subscribe($event->courseId, $course->getName());
        $this->projections->store($student);
    }

    protected function handleStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        $student = $this->findStudent($event->studentId);
        $student->unsubscribe($event->courseId);
        $this->projections->store($student);
    }

    private function findStudent(string $studentId): StudentProjection
    {
        /** @var StudentProjection $p */
        $p = $this->projections->find(StudentProjection::id($studentId), StudentProjection::class);
        return $p;
    }

    private function findCourse(string $courseId): CourseInternalProjection
    {
        /** @var CourseInternalProjection $p */
        $p = $this->projections->find($courseId, CourseInternalProjection::class);
        return $p;
    }
}
