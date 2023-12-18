<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class TaskRenamedEvent implements EventInterface
{
    use ToArrayTrait;

    private string $previousName;

    private string $newName;

    public function __construct(string $previousName, string $newName)
    {
        $this->previousName = $previousName;
        $this->newName = $newName;
    }

    public function getPreviousName(): string
    {
        return $this->previousName;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }
}
