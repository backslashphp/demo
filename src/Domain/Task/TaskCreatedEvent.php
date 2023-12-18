<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class TaskCreatedEvent implements EventInterface
{
    use ToArrayTrait;

    private int $number;

    private string $name;

    private string $projectId;

    public function __construct(int $number, string $name, string $projectId)
    {
        $this->number = $number;
        $this->name = $name;
        $this->projectId = $projectId;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}
