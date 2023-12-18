<?php

declare(strict_types=1);

namespace Demo\Domain\Project;

use Backslash\AggregateStore\AggregateStore;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventSourcingAggregateStore\EventSourcingAggregateStoreBuilder;
use Backslash\EventStore\EventStoreInterface;

class ProjectRepository
{
    private AggregateStore $aggregates;

    public function __construct(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $this->aggregates = EventSourcingAggregateStoreBuilder::build(
            Project::class,
            $eventStore,
            $eventBus,
        );
    }

    public function find(string $projectId): Project
    {
        return $this->aggregates->find($projectId, Project::getType());
    }

    public function store(Project $project): void
    {
        $this->aggregates->store($project);
    }
}
