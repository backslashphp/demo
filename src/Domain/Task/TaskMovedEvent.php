<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class TaskMovedEvent implements EventInterface
{
    use ToArrayTrait;

    private string $previousProjectId;

    private string $newProjectId;

    public function __construct(string $previousProjectId, string $newProjectId)
    {
        $this->previousProjectId = $previousProjectId;
        $this->newProjectId = $newProjectId;
    }

    public function getPreviousProjectId(): string
    {
        return $this->previousProjectId;
    }

    public function getNewProjectId(): string
    {
        return $this->newProjectId;
    }
}
