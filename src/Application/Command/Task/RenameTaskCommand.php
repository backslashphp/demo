<?php

declare(strict_types=1);

namespace Demo\Application\Command\Task;

class RenameTaskCommand
{
    private string $taskId;

    private string $name;

    public function __construct(string $taskId, string $name)
    {
        $this->taskId = $taskId;
        $this->name = $name;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
