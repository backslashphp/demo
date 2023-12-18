<?php

declare(strict_types=1);

namespace Demo\Domain\Project;

use Backslash\Aggregate\EventInterface;
use Backslash\Aggregate\ToArrayTrait;

class ProjectStatusChangedEvent implements EventInterface
{
    use ToArrayTrait;

    private string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
