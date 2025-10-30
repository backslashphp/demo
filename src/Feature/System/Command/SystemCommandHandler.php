<?php

declare(strict_types=1);

namespace Demo\Feature\System\Command;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\Pdo\PdoInterface;
use Backslash\PdoEventStore\Config;
use Backslash\PdoEventStore\Driver;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\Repository\RepositoryInterface;
use Demo\Feature\Shared\Command\AbstractCommandHandler;
use Demo\Feature\Shared\Projection\CourseList\CourseListProjection;
use Demo\Feature\Shared\Projection\StudentList\StudentListProjection;

class SystemCommandHandler extends AbstractCommandHandler
{
    private ProjectionStoreInterface $projections;

    private EventStoreInterface $eventStore;

    private DispatcherInterface $dispatcher;

    private PdoInterface $pdo;

    public function __construct(
        RepositoryInterface $repository,
        ProjectionStoreInterface $projections,
        EventStoreInterface $eventStore,
        DispatcherInterface $dispatcher,
        PdoInterface $pdo,
    ) {
        parent::__construct($repository);
        $this->projections = $projections;
        $this->eventStore = $eventStore;
        $this->dispatcher = $dispatcher;
        $this->pdo = $pdo;
    }

    protected function handleCreateDatabaseCommand(CreateDatabaseCommand $command): void
    {
        $this->pdo->exec(Driver::SQLITE->buildCreateTableStatement(new Config()));
        $this->pdo->exec(file_get_contents('resources/create_table_projection_store.sql'));
    }

    protected function handleInitializeProjectionsCommand(InitializeProjectionsCommand $command): void
    {
        $projections = [
            CourseListProjection::class,
            StudentListProjection::class,
        ];
        foreach ($projections as $projection) {
            if (!$this->projections->has($projection::ID, $projection)) {
                $this->projections->store(new $projection());
            }
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
