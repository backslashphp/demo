<?php

declare(strict_types=1);

namespace Demo\Domain\Project;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class ProjectCreatedEvent implements EventInterface
{
    use ToArrayTrait;

    private int $number;

    private string $name;

    public function __construct(int $number, string $name)
    {
        $this->number = $number;
        $this->name = $name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
