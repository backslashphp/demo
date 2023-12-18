<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Application\IdResolver;

use Backslash\Projection\ProjectionInterface;

class IdsProjection implements ProjectionInterface
{
    public const ID = __CLASS__;

    private array $projectIds = [];

    private array $taskIds = [];

    public function getId(): string
    {
        return self::ID;
    }

    public function addProject(string $projectId, int $number): void
    {
        $this->projectIds[$number] = $projectId;
    }

    public function addTask(string $taskId, int $number): void
    {
        $this->taskIds[$number] = $taskId;
    }

    public function removeTask(string $taskId): void
    {
        $key = array_search($taskId, $this->taskIds);
        unset($this->taskIds[$key]);
    }

    public function getProjectId(int $number): ?string
    {
        return $this->projectIds[$number] ?? null;
    }

    public function getTaskId(int $number): ?string
    {
        return $this->taskIds[$number] ?? null;
    }
}
