<?php

declare(strict_types=1);

namespace Demo\Feature\CourseListView\Projection;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;
use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;

class CourseListProjector implements EventHandlerInterface
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
        $list->addCourse($event->courseId);
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
