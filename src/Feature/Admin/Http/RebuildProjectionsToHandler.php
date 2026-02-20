<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\EventStore\Query\Sequence;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\StreamEnricher\StreamEnricherInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RebuildProjectionsToHandler implements RequestHandlerInterface
{
    use RebuildProjectionsTrait;

    private DispatcherInterface $dispatcher;

    private EventStoreInterface $eventStore;

    private EventBusInterface $eventBus;

    private StreamEnricherInterface $enricher;

    private ProjectionStoreInterface $projections;

    public function __construct(
        DispatcherInterface $dispatcher,
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus,
        StreamEnricherInterface $enricher,
        ProjectionStoreInterface $projections,
    ) {
        $this->dispatcher = $dispatcher;
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->enricher = $enricher;
        $this->projections = $projections;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $toSequence = (int) $request->getAttribute('sequence');

        $this->rebuild(Sequence::upTo($toSequence));

        return new EmptyResponse();
    }
}
