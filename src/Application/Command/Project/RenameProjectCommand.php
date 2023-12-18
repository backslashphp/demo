<?php

declare(strict_types=1);

namespace Demo\Application\Command\Project;

class RenameProjectCommand
{
    private string $projectId;

    private string $name;

    public function __construct(string $projectId, string $name)
    {
        $this->projectId = $projectId;
        $this->name = $name;
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
