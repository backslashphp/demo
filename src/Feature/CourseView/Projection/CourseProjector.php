<?php

declare(strict_types=1);

namespace Demo\Feature\CourseView\Projection;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;

class CourseProjector implements EventHandlerInterface
{
    use EventHandlerTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    protected function handleCourseDefinedEvent(CourseDefinedEvent $event): void
    {
        $projection = new CourseProjection($event->courseId, $event->name, $event->capacity);
        $this->projections->store($projection);
    }

    protected function handleCourseCapacityChangedEvent(CourseCapacityChangedEvent $event): void
    {
        $course = $this->findCourse($event->courseId);
        $course->changeCapacity($event->new);
        $this->projections->store($course);
    }

    protected function handleStudentSubscribedToCourseEvent(StudentSubscribedToCourseEvent $event): void
    {
        $course = $this->findCourse($event->courseId);
        $course->subscribe($event->studentId);
        $this->projections->store($course);
    }

    protected function handleStudentUnsubscribedFromCourseEvent(StudentUnsubscribedFromCourseEvent $event): void
    {
        $course = $this->findCourse($event->courseId);
        $course->unsubscribe($event->studentId);
        $this->projections->store($course);
    }

    private function findCourse(string $courseId): CourseProjection
    {
        /** @var CourseProjection $p */
        $p = $this->projections->find(CourseProjection::id($courseId), CourseProjection::class);
        return $p;
    }
}
