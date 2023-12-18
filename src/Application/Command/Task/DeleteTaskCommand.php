<?php

declare(strict_types=1);

namespace Demo\Application\Command\Task;

class DeleteTaskCommand
{
    private string $taskId;

    public function __construct(string $taskId)
    {
        $this->taskId = $taskId;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }
}
