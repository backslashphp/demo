<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CacheProjectionStoreMiddleware\CacheProjectionStoreMiddleware;
use Backslash\CommandDispatcher\Dispatcher;
use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\CommandDispatcher\HandlerProxy;
use Backslash\EventBus\EventBus;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventBus\EventHandlerProxy;
use Backslash\EventStore\EventStore;
use Backslash\EventStore\EventStoreInterface;
use Backslash\Pdo\PdoInterface;
use Backslash\Pdo\PdoProxy;
use Backslash\PdoEventStore\Config as PdoEventStoreConfig;
use Backslash\PdoEventStore\JsonEventSerializer;
use Backslash\PdoEventStore\JsonMetadataSerializer;
use Backslash\PdoEventStore\PdoEventStoreAdapter;
use Backslash\PdoProjectionStore\Config as PdoProjectionStoreConfig;
use Backslash\PdoProjectionStore\PdoProjectionStoreAdapter;
use Backslash\ProjectionStore\ProjectionStore;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\ProjectionStoreTransactionCommandDispatcherMiddleware\ProjectionStoreTransactionCommandDispatcherMiddleware;
use Backslash\Serializer\SerializeFunctionSerializer;
use Backslash\Serializer\Serializer;
use Backslash\StreamEnricher\StreamEnricherEventBusMiddleware;
use Backslash\StreamEnricher\StreamEnricherEventStoreMiddleware;
use Backslash\StreamEnricher\StreamEnricherInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Application\Command\AbstractCommandHandler;
use Demo\Application\Command\Project\ProjectCommandHandler;
use Demo\Application\Command\System\SystemCommandHandler;
use Demo\Application\Command\Task\TaskCommandHandler;
use Demo\Application\IdResolver\IdResolverInterface;
use Demo\Application\Processor\ProjectStatus\ProjectStatusProcessor;
use Demo\Domain\Project\ProjectRepository;
use Demo\Domain\Sequence\SequenceNumberInterface;
use Demo\Domain\Task\TaskRepository;
use Demo\Infrastructure\Application\IdResolver\IdResolver;
use Demo\Infrastructure\Application\IdResolver\IdResolverProjector;
use Demo\Infrastructure\Domain\Sequence\SequenceNumber;
use Demo\Infrastructure\Domain\Sequence\SequenceNumberProjector;
use Demo\UI\Projection\ProjectList\ProjectListProjector;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class Container implements ContainerInterface
{
    private const COMMAND_HANDLERS = [
        ProjectCommandHandler::class,
        SystemCommandHandler::class,
        TaskCommandHandler::class,
    ];

    private const PROJECTORS = [
        IdResolverProjector::class,
        ProjectListProjector::class,
        SequenceNumberProjector::class,
    ];

    private const PROCESSORS = [
        ProjectStatusProcessor::class,
    ];

    private array $cache = [];

    public function __construct()
    {
        $this->configureCommandHandlers();
        $this->configureProjectors();
        $this->configureProcessors();
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException($id);
        }
        if (!isset($this->cache[$id])) {
            $resolver = $this->getServices()[$id];
            $this->cache[$id] = $resolver($this);
        }
        return $this->cache[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->getServices()[$id]);
    }

    private function configureCommandHandlers(): void
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->get(DispatcherInterface::class);
        /** @var AbstractCommandHandler|string $handlerClass */
        foreach (self::COMMAND_HANDLERS as $handlerClass) {
            foreach ($handlerClass::getHandledCommands() as $commandClass) {
                $dispatcher->registerHandler($commandClass, new HandlerProxy(fn () => $this->get($handlerClass)));
            }
        }
    }

    private function configureProjectors(): void
    {
        /** @var EventBusInterface $eventBus */
        $eventBus = $this->get(EventBusInterface::class);
        /** @var AbstractEventHandler|string $projectorClass */
        foreach (self::PROJECTORS as $projectorClass) {
            foreach ($projectorClass::getSubscribedEvents() as $eventClass) {
                $eventBus->subscribe($eventClass, new EventHandlerProxy(fn () => $this->get($projectorClass)));
            }
        }
    }

    private function configureProcessors(): void
    {
        /** @var EventBusInterface $eventBus */
        $eventBus = $this->get(EventBusInterface::class);
        /** @var AbstractEventHandler|string $processorClass */
        foreach (self::PROCESSORS as $processorClass) {
            foreach ($processorClass::getSubscribedEvents() as $eventClass) {
                $eventBus->subscribe($eventClass, new EventHandlerProxy(fn () => $this->get($processorClass)));
            }
        }
    }

    private function getServices(): array
    {
        return [
            DispatcherInterface::class => function (ContainerInterface $c) {
                $dispatcher = new Dispatcher();
                $dispatcher->addMiddleware(
                    new ProjectionStoreTransactionCommandDispatcherMiddleware($c->get(ProjectionStoreInterface::class)),
                );
                return $dispatcher;
            },
            EventBusInterface::class => function (ContainerInterface $c) {
                $bus = new EventBus();
                $bus->addMiddleware(new StreamEnricherEventBusMiddleware($c->get(StreamEnricherInterface::class)));
                return $bus;
            },
            EventStoreInterface::class => function (ContainerInterface $c) {
                $store = new EventStore(
                    new PdoEventStoreAdapter(
                        $c->get(PdoInterface::class),
                        new PdoEventStoreConfig(),
                        new Serializer(new JsonEventSerializer()),
                        new Serializer(new JsonMetadataSerializer()),
                        fn () => Uuid::uuid4()->toString(),
                    ),
                );
                $store->addMiddleware(new StreamEnricherEventStoreMiddleware($c->get(StreamEnricherInterface::class)));
                return $store;
            },
            IdResolverInterface::class => fn (ContainerInterface $c) => new IdResolver(
                $c->get(ProjectionStoreInterface::class),
            ),
            IdResolverProjector::class => fn (ContainerInterface $c) => new IdResolverProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            PdoInterface::class => fn () => new PdoProxy(fn () => new PDO('sqlite:data/demo.sqlite')),
            ProjectCommandHandler::class => fn (ContainerInterface $c) => new ProjectCommandHandler(
                $c->get(ProjectRepository::class),
                $c->get(SequenceNumberInterface::class),
            ),
            ProjectionStoreInterface::class => function (ContainerInterface $c) {
                $store = new ProjectionStore(
                    new PdoProjectionStoreAdapter(
                        $c->get(PdoInterface::class),
                        new Serializer(new SerializeFunctionSerializer()),
                        new PdoProjectionStoreConfig(),
                    ),
                );
                $store->addMiddleware(new CacheProjectionStoreMiddleware());
                return $store;
            },
            ProjectListProjector::class => fn (ContainerInterface $c) => new ProjectListProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            ProjectRepository::class => fn (ContainerInterface $c) => new ProjectRepository(
                $c->get(EventStoreInterface::class),
                $c->get(EventBusInterface::class),
            ),
            ProjectStatusProcessor::class => fn (ContainerInterface $c) => new ProjectStatusProcessor(
                $c->get(ProjectionStoreInterface::class),
                $c->get(ProjectRepository::class),
            ),
            SequenceNumberInterface::class => fn (ContainerInterface $c) => new SequenceNumber(
                $c->get(ProjectionStoreInterface::class),
            ),
            SequenceNumberProjector::class => fn (ContainerInterface $c) => new SequenceNumberProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            StreamEnricherInterface::class => fn () => new StreamEnricher(),
            SystemCommandHandler::class => fn (ContainerInterface $c) => new SystemCommandHandler(
                $c->get(ProjectionStoreInterface::class),
                $c->get(EventStoreInterface::class),
                $c->get(DispatcherInterface::class),
                $c->get(PdoInterface::class),
            ),
            TaskCommandHandler::class => fn (ContainerInterface $c) => new TaskCommandHandler(
                $c->get(TaskRepository::class),
                $c->get(SequenceNumberInterface::class),
            ),
            TaskRepository::class => fn (ContainerInterface $c) => new TaskRepository(
                $c->get(EventStoreInterface::class),
                $c->get(EventBusInterface::class),
            ),
        ];
    }
}
