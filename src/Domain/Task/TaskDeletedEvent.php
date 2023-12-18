<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class TaskDeletedEvent implements EventInterface
{
    use ToArrayTrait;

    private string $projectId;

    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}
