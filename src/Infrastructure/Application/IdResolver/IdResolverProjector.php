<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Application\IdResolver;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Project\ProjectCreatedEvent;
use Demo\Domain\Task\TaskCreatedEvent;
use Demo\Domain\Task\TaskDeletedEvent;

class IdResolverProjector extends AbstractEventHandler
{
    use GetIdsTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProjectCreatedEvent::class,
            TaskCreatedEvent::class,
            TaskDeletedEvent::class,
        ];
    }

    protected function handleProjectCreatedEvent(string $projectId, ProjectCreatedEvent $event): void
    {
        $ids = $this->getIds();
        $ids->addProject($projectId, $event->getNumber());
        $this->projections->store($ids);
    }

    protected function handleTaskCreatedEvent(string $taskId, TaskCreatedEvent $event): void
    {
        $ids = $this->getIds();
        $ids->addTask($taskId, $event->getNumber());
        $this->projections->store($ids);
    }

    protected function handleTaskDeletedEvent(string $taskId, TaskDeletedEvent $event): void
    {
        $ids = $this->getIds();
        $ids->removeTask($taskId);
        $this->projections->store($ids);
    }
}
