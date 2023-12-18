<?php

declare(strict_types=1);

namespace Demo\Application\Processor\ProjectStatus;

use Backslash\Projection\ProjectionInterface;
use Demo\Domain\Project\ProjectStatus;
use Demo\Domain\Task\TaskStatus;

class ProjectTasksInternalProjection implements ProjectionInterface
{
    private string $projectId;

    private array $tasks = [];

    private ProjectStatus $status;

    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
        $this->status = ProjectStatus::PENDING;
    }

    public function getId(): string
    {
        return $this->projectId;
    }

    public function setProjectStatus(ProjectStatus $status): void
    {
        $this->status = $status;
    }

    public function setTaskStatus(string $taskId, TaskStatus $status): void
    {
        $this->tasks[$taskId] = $status;
    }

    public function removeTask(string $taskId): void
    {
        unset($this->tasks[$taskId]);
    }

    public function resolveNewProjectStatus(): ?ProjectStatus
    {
        if (($this->getCompletedTaskCount() === count($this->tasks)) && ($this->status !== ProjectStatus::COMPLETED)) {
            return ProjectStatus::COMPLETED;
        }
        if (($this->getStartedTaskCount() || $this->getCompletedTaskCount(
        )) && ($this->status !== ProjectStatus::STARTED)) {
            return ProjectStatus::STARTED;
        }
        if (($this->getPendingTaskCount() === count($this->tasks)) && ($this->status !== ProjectStatus::PENDING)) {
            return ProjectStatus::PENDING;
        }
        return null;
    }

    private function getPendingTaskCount(): int
    {
        return count(array_filter($this->tasks, fn (TaskStatus $status) => $status === TaskStatus::PENDING));
    }

    private function getStartedTaskCount(): int
    {
        return count(array_filter($this->tasks, fn (TaskStatus $status) => $status === TaskStatus::STARTED));
    }

    private function getCompletedTaskCount(): int
    {
        return count(array_filter($this->tasks, fn (TaskStatus $status) => $status === TaskStatus::COMPLETED));
    }
}
