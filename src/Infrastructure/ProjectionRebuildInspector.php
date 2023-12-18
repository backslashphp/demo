<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\Aggregate\RecordedEvent;
use Backslash\EventStore\Filter;
use Backslash\EventStore\InspectorInterface;

class ProjectionRebuildInspector implements InspectorInterface
{
    private Filter $filter;

    private $callable;

    public function __construct(Filter $filter, callable $callable)
    {
        $this->filter = $filter;
        $this->callable = $callable;
    }

    public function getFilter(): Filter
    {
        return $this->filter;
    }

    public function inspect(string $aggregateId, string $aggregateType, RecordedEvent $recordedEvent): void
    {
        $callable = $this->callable;
        $callable($aggregateId, $aggregateType, $recordedEvent);
    }
}
