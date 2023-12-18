<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\AggregateStore\AggregateStore;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventSourcingAggregateStore\EventSourcingAggregateStoreBuilder;
use Backslash\EventStore\EventStoreInterface;

class TaskRepository
{
    private AggregateStore $aggregates;

    public function __construct(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $this->aggregates = EventSourcingAggregateStoreBuilder::build(
            Task::class,
            $eventStore,
            $eventBus,
        );
    }

    public function find(string $taskId): Task
    {
        return $this->aggregates->find($taskId, Task::getType());
    }

    public function store(Task $task): void
    {
        $this->aggregates->store($task);
    }
}
