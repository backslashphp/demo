<?php

declare(strict_types=1);

namespace Demo\Application\Command\Project;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\Project\Project;
use Demo\Domain\Project\ProjectRepository;
use Demo\Domain\Sequence\SequenceNumberInterface;

class ProjectCommandHandler extends AbstractCommandHandler
{
    private ProjectRepository $projects;

    private SequenceNumberInterface $sequence;

    public function __construct(ProjectRepository $projects, SequenceNumberInterface $sequence)
    {
        $this->projects = $projects;
        $this->sequence = $sequence;
    }

    public static function getHandledCommands(): array
    {
        return [
            CreateProjectCommand::class,
            RenameProjectCommand::class,
        ];
    }

    protected function handleCreateProjectCommand(CreateProjectCommand $command): void
    {
        $project = Project::create($command->getProjectId(), $command->getName(), $this->sequence);
        $this->projects->store($project);
    }

    protected function handleRenameProjectCommand(RenameProjectCommand $command): void
    {
        $project = $this->projects->find($command->getProjectId());
        $project->rename($command->getName());
        $this->projects->store($project);
    }
}
