<?php

declare(strict_types=1);

namespace Demo\Application\Command\Task;

class CreateTaskCommand
{
    private string $taskId;

    private string $projectId;

    private string $name;

    public function __construct(string $taskId, string $projectId, string $name)
    {
        $this->taskId = $taskId;
        $this->projectId = $projectId;
        $this->name = $name;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
