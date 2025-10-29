<?php

declare(strict_types=1);

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\Event\RecordedEvent;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\StreamEnricher\StreamEnricherInterface;
use Backslash\StreamPublishingInspection\Inspector;
use Demo\Infrastructure\StreamEnricher;
use Demo\Infrastructure\System\InitializeProjectionsCommand;
use Demo\Infrastructure\System\PurgeProjectionsCommand;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var DispatcherInterface $dispatcher */
$dispatcher = $container->get(DispatcherInterface::class);
/** @var EventStoreInterface $eventStore */
$eventStore = $container->get(EventStoreInterface::class);
/** @var EventBusInterface $eventBus */
$eventBus = $container->get(EventBusInterface::class);
/** @var StreamEnricher $enricher */
$enricher = $container->get(StreamEnricherInterface::class);

$dispatcher->dispatch(new PurgeProjectionsCommand());
$dispatcher->dispatch(new InitializeProjectionsCommand());
$enricher->disable();

$count = 0;
$inspector = new Inspector(
    $eventBus,
    null,
    function (RecordedEvent $recordedEvent) use (&$count): void {
        echo str_pad('No:', 20) . ++$count . PHP_EOL;
        echo str_pad('Event:', 20) . $recordedEvent->getEvent()::class . PHP_EOL;
        echo str_pad('Timestamp:', 20) . $recordedEvent->getRecordTime()->format('Y-m-d\TH:i:s.uP') . PHP_EOL . PHP_EOL;
    },
);
$eventStore->inspect($inspector);

/** @var ProjectionStoreInterface $projections */
$projections = $container->get(ProjectionStoreInterface::class);
$projections->commit();
