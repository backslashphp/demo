<?php

declare(strict_types=1);

namespace Demo\Application\Processor\ProjectStatus;

use Backslash\ProjectionStore\ProjectionNotFoundException;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Project\ProjectCreatedEvent;
use Demo\Domain\Project\ProjectRepository;
use Demo\Domain\Project\ProjectStatus;
use Demo\Domain\Project\ProjectStatusChangedEvent;
use Demo\Domain\Task\TaskCompletedEvent;
use Demo\Domain\Task\TaskCreatedEvent;
use Demo\Domain\Task\TaskDeletedEvent;
use Demo\Domain\Task\TaskMovedEvent;
use Demo\Domain\Task\TaskStartedEvent;
use Demo\Domain\Task\TaskStatus;

class ProjectStatusProcessor extends AbstractEventHandler
{
    private ProjectionStoreInterface $projections;

    private ProjectRepository $projects;

    public function __construct(ProjectionStoreInterface $projections, ProjectRepository $projects)
    {
        $this->projections = $projections;
        $this->projects = $projects;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProjectCreatedEvent::class,
            ProjectStatusChangedEvent::class,
            TaskCompletedEvent::class,
            TaskCreatedEvent::class,
            TaskDeletedEvent::class,
            TaskMovedEvent::class,
            TaskStartedEvent::class,
        ];
    }

    protected function handleProjectCreatedEvent(string $projectId, ProjectCreatedEvent $event): void
    {
        $this->projections->store(new ProjectTasksInternalProjection($projectId));
    }

    protected function handleProjectStatusChangedEvent(string $projectId, ProjectStatusChangedEvent $event): void
    {
        $p = $this->getProject($projectId);
        $p->setProjectStatus(ProjectStatus::from($event->getStatus()));
        $this->projections->store($p);
    }

    protected function handleTaskCompletedEvent(string $taskId, TaskCompletedEvent $event): void
    {
        $p = $this->getProject($event->getProjectId());
        $p->setTaskStatus($taskId, TaskStatus::COMPLETED);
        $this->projections->store($p);
        $this->changeProjectStatus($p);
    }

    protected function handleTaskCreatedEvent(string $taskId, TaskCreatedEvent $event): void
    {
        $p = $this->getProject($event->getProjectId());
        $p->setTaskStatus($taskId, TaskStatus::PENDING);
        $this->projections->store($p);
        $this->changeProjectStatus($p);
    }

    protected function handleTaskDeletedEvent(string $taskId, TaskDeletedEvent $event): void
    {
        $p = $this->getProject($event->getProjectId());
        $p->removeTask($taskId);
        $this->projections->store($p);
        $this->changeProjectStatus($p);
    }

    protected function handleTaskMovedEvent(string $taskId, TaskMovedEvent $event): void
    {
        $p = $this->getProject($event->getPreviousProjectId());
        $p->removeTask($taskId);
        $this->projections->store($p);
        $this->changeProjectStatus($p);

        $p = $this->getProject($event->getNewProjectId());
        $this->projections->store($p);
        $this->changeProjectStatus($p);
    }

    protected function handleTaskStartedEvent(string $taskId, TaskStartedEvent $event): void
    {
        $p = $this->getProject($event->getProjectId());
        $p->setTaskStatus($taskId, TaskStatus::STARTED);
        $this->projections->store($p);
        $this->changeProjectStatus($p);
    }

    private function getProject($projectId): ProjectTasksInternalProjection
    {
        try {
            /** @var ProjectTasksInternalProjection $p */
            $p = $this->projections->find($projectId, ProjectTasksInternalProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new ProjectTasksInternalProjection($projectId);
        }
        return $p;
    }

    private function changeProjectStatus(ProjectTasksInternalProjection $p): void
    {
        $newStatus = $p->resolveNewProjectStatus();
        if ($newStatus) {
            $project = $this->projects->find($p->getId());
            $project->changeStatus($newStatus);
            $this->projects->store($project);
        }
    }
}
