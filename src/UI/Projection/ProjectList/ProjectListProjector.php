<?php

declare(strict_types=1);

namespace Demo\UI\Projection\ProjectList;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Project\ProjectCreatedEvent;
use Demo\Domain\Project\ProjectRenamedEvent;
use Demo\Domain\Project\ProjectStatusChangedEvent;
use Demo\Domain\Task\TaskCompletedEvent;
use Demo\Domain\Task\TaskCreatedEvent;
use Demo\Domain\Task\TaskDeletedEvent;
use Demo\Domain\Task\TaskMovedEvent;
use Demo\Domain\Task\TaskRenamedEvent;
use Demo\Domain\Task\TaskStartedEvent;
use Demo\Domain\Task\TaskStatus;

class ProjectListProjector extends AbstractEventHandler
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProjectCreatedEvent::class,
            ProjectRenamedEvent::class,
            ProjectStatusChangedEvent::class,
            TaskCreatedEvent::class,
            TaskDeletedEvent::class,
            TaskMovedEvent::class,
            TaskRenamedEvent::class,
            TaskCompletedEvent::class,
            TaskStartedEvent::class,
        ];
    }

    protected function handleProjectCreatedEvent(string $projectId, ProjectCreatedEvent $event): void
    {
        $list = $this->getList();
        $list->addProject($projectId, $event->getNumber(), $event->getName());
        $this->projections->store($list);
    }

    protected function handleProjectRenamedEvent(string $projectId, ProjectRenamedEvent $event): void
    {
        $list = $this->getList();
        $list->renameProject($projectId, $event->getNewName());
        $this->projections->store($list);
    }

    protected function handleProjectStatusChangedEvent(string $projectId, ProjectStatusChangedEvent $event): void
    {
        $list = $this->getList();
        $list->setProjectStatus($projectId, $event->getStatus());
        $this->projections->store($list);
    }

    protected function handleTaskCreatedEvent(string $taskId, TaskCreatedEvent $event): void
    {
        $list = $this->getList();
        $list->addTaskToProject($taskId, $event->getNumber(), $event->getName(), $event->getProjectId());
        $this->projections->store($list);
    }

    protected function handleTaskDeletedEvent(string $taskId, TaskDeletedEvent $event): void
    {
        $list = $this->getList();
        $list->removeTaskFromProject($taskId, $event->getProjectId());
        $this->projections->store($list);
    }

    protected function handleTaskMovedEvent(string $taskId, TaskMovedEvent $event): void
    {
        $list = $this->getList();
        $list->moveTaskBetweenProjects($taskId, $event->getPreviousProjectId(), $event->getNewProjectId());
        $this->projections->store($list);
    }

    protected function handleTaskRenamedEvent(string $taskId, TaskRenamedEvent $event): void
    {
        $list = $this->getList();
        $list->renameTask($taskId, $event->getNewName());
        $this->projections->store($list);
    }

    protected function handleTaskCompletedEvent(string $taskId, TaskCompletedEvent $event): void
    {
        $list = $this->getList();
        $list->setTaskStatus($taskId, TaskStatus::COMPLETED->value);
        $this->projections->store($list);
    }

    protected function handleTaskStartedEvent(string $taskId, TaskStartedEvent $event): void
    {
        $list = $this->getList();
        $list->setTaskStatus($taskId, TaskStatus::STARTED->value);
        $this->projections->store($list);
    }

    private function getList(): ProjectListProjection
    {
        try {
            /** @var ProjectListProjection $p */
            $p = $this->projections->find(ProjectListProjection::ID, ProjectListProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new ProjectListProjection();
        }
        return $p;
    }
}
