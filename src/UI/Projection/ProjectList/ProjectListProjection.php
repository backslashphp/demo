<?php

declare(strict_types=1);

namespace Demo\UI\Projection\ProjectList;

use Backslash\Projection\ProjectionInterface;
use Demo\Domain\Project\ProjectStatus;
use Demo\Domain\Task\TaskStatus;
use Stringable;

class ProjectListProjection implements ProjectionInterface, Stringable, \Countable
{
    public const ID = __CLASS__;

    private array $projects = [];

    private array $tasks = [];

    public function __toString(): string
    {
        $string = '';
        foreach ($this->projects as $project) {
            $string .= sprintf('[%s] %s (%s)', $project['number'], $project['name'], $project['status']) . PHP_EOL;
            foreach ($project['tasks'] as $taskId) {
                $task = $this->tasks[$taskId];
                $string .= sprintf('- [%s] %s (%s)', $task['number'], $task['name'], $task['status']) . PHP_EOL;
            }
            $string .= PHP_EOL;
        }
        return $string;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function count(): int
    {
        return count($this->projects);
    }

    public function addProject(string $projectId, int $number, string $name): void
    {
        $this->projects[$projectId] = [
            'number' => $number,
            'name' => $name,
            'status' => ProjectStatus::PENDING->value,
            'tasks' => [],
        ];
    }

    public function renameProject(string $projectId, string $name): void
    {
        $this->projects[$projectId]['name'] = $name;
    }

    public function setProjectStatus(string $projectId, string $status): void
    {
        $this->projects[$projectId]['status'] = $status;
    }

    public function addTaskToProject(string $taskId, int $number, string $name, string $projectId): void
    {
        $this->projects[$projectId]['tasks'][$taskId] = $taskId;
        $this->tasks[$taskId] = [
            'number' => $number,
            'name' => $name,
            'status' => TaskStatus::PENDING->value,
            'projectId' => $projectId,
        ];
    }

    public function removeTaskFromProject(string $taskId, string $projectId): void
    {
        unset($this->projects[$projectId]['tasks'][$taskId]);
        $this->tasks[$taskId]['projectId'] = null;
    }

    public function moveTaskBetweenProjects(string $taskId, string $fromProjectId, string $toProjectId): void
    {
        $this->projects[$toProjectId]['tasks'][$taskId] = $taskId;
        $this->tasks[$taskId]['projectId'] = $toProjectId;
        $this->removeTaskFromProject($taskId, $fromProjectId);
    }

    public function renameTask(string $taskId, string $name): void
    {
        $this->tasks[$taskId]['name'] = $name;
    }

    public function setTaskStatus(string $taskId, string $status): void
    {
        $this->tasks[$taskId]['status'] = $status;
    }

    public function getProjectCount(): int
    {
        return count($this->projects);
    }
}
