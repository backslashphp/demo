<?php

declare(strict_types=1);

namespace Demo\Application\Command\Task;

class MoveTaskCommand
{
    private string $taskId;

    private string $projectId;

    public function __construct(string $taskId, string $projectId)
    {
        $this->taskId = $taskId;
        $this->projectId = $projectId;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}
