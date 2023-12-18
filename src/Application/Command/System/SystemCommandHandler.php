<?php

declare(strict_types=1);

namespace Demo\Application\Command\System;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\Pdo\PdoInterface;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\Command\AbstractCommandHandler;
use Demo\UI\Projection\ProjectList\ProjectListProjection;

class SystemCommandHandler extends AbstractCommandHandler
{
    private ProjectionStoreInterface $projections;

    private EventStoreInterface $eventStore;

    private DispatcherInterface $dispatcher;

    private PdoInterface $pdo;

    public function __construct(
        ProjectionStoreInterface $projections,
        EventStoreInterface $eventStore,
        DispatcherInterface $dispatcher,
        PdoInterface $pdo,
    ) {
        $this->projections = $projections;
        $this->eventStore = $eventStore;
        $this->dispatcher = $dispatcher;
        $this->pdo = $pdo;
    }

    public static function getHandledCommands(): array
    {
        return [
            CreateDatabaseCommand::class,
            InitializeProjectionsCommand::class,
            PurgeEventsCommand::class,
            PurgeProjectionsCommand::class,
            ResetCommand::class,
        ];
    }

    protected function handleCreateDatabaseCommand(CreateDatabaseCommand $command): void
    {
        $this->pdo->exec(file_get_contents('resources/create_table_event_store.sql'));
        $this->pdo->exec(file_get_contents('resources/create_table_projection_store.sql'));
    }

    protected function handleInitializeProjectionsCommand(InitializeProjectionsCommand $command): void
    {
        if (!$this->projections->has(ProjectListProjection::ID, ProjectListProjection::class)) {
            $this->projections->store(new ProjectListProjection());
        }
    }

    protected function handlePurgeEventsCommand(PurgeEventsCommand $command): void
    {
        $this->eventStore->purge();
    }

    protected function handlePurgeProjectionsCommand(PurgeProjectionsCommand $command): void
    {
        $this->projections->purge();
    }

    protected function handleResetCommand(ResetCommand $command): void
    {
        $this->dispatcher->dispatch(new CreateDatabaseCommand());
        $this->dispatcher->dispatch(new PurgeEventsCommand());
        $this->dispatcher->dispatch(new PurgeProjectionsCommand());
        $this->dispatcher->dispatch(new InitializeProjectionsCommand());
    }
}
