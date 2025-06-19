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
use Backslash\PdoEventStore\JsonIdentifiersSerializer;
use Backslash\PdoEventStore\JsonMetadataSerializer;
use Backslash\PdoEventStore\PdoEventStoreAdapter;
use Backslash\PdoProjectionStore\Config as PdoProjectionStoreConfig;
use Backslash\PdoProjectionStore\PdoProjectionStoreAdapter;
use Backslash\ProjectionStore\ProjectionStore;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\ProjectionStoreTransactionCommandDispatcherMiddleware\ProjectionStoreTransactionCommandDispatcherMiddleware;
use Backslash\Repository\Repository;
use Backslash\Repository\RepositoryInterface;
use Backslash\Serializer\SerializeFunctionSerializer;
use Backslash\Serializer\Serializer;
use Backslash\StreamEnricher\StreamEnricherEventBusMiddleware;
use Backslash\StreamEnricher\StreamEnricherEventStoreMiddleware;
use Backslash\StreamEnricher\StreamEnricherInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Application\Command\AbstractCommandHandler;
use Demo\Application\Command\Course\CourseCommandHandler;
use Demo\Application\Command\Subscription\SubscriptionCommandHandler;
use Demo\Application\Command\Student\StudentCommandHandler;
use Demo\Application\Command\System\SystemCommandHandler;
use Demo\UI\Projection\CourseList\CourseListProjector;
use Demo\UI\Projection\StudentList\StudentListProjector;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class Container implements ContainerInterface
{
    private const COMMAND_HANDLERS = [
        CourseCommandHandler::class,
        StudentCommandHandler::class,
        SubscriptionCommandHandler::class,
        SystemCommandHandler::class,
    ];

    private const PROJECTORS = [
        CourseListProjector::class,
        StudentListProjector::class,
    ];

    private const PROCESSORS = [
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
            CourseCommandHandler::class => fn (ContainerInterface $c) => new CourseCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            CourseListProjector::class => fn (ContainerInterface $c) => new CourseListProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            DispatcherInterface::class => function (ContainerInterface $c) {
                $dispatcher = new Dispatcher();
                $dispatcher->addMiddleware($c->get(ExitOnErrorCommandDispatcherMiddleware::class));
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
                        new Serializer(new JsonIdentifiersSerializer()),
                        new Serializer(new JsonMetadataSerializer()),
                        fn () => Uuid::uuid4()->toString(),
                    ),
                );
                $store->addMiddleware(new StreamEnricherEventStoreMiddleware($c->get(StreamEnricherInterface::class)));
                return $store;
            },
            ExitOnErrorCommandDispatcherMiddleware::class => fn () => new ExitOnErrorCommandDispatcherMiddleware(),
            PdoInterface::class => function () {
                $dsn = getenv('TESTING') ? 'sqlite::memory:' : 'sqlite:data/demo.sqlite';
                return new PdoProxy(fn () => new PDO($dsn));
            },
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
            RepositoryInterface::class => fn (ContainerInterface $c) => new Repository(
                $c->get(EventStoreInterface::class),
                $c->get(EventBusInterface::class),
            ),
            StreamEnricherInterface::class => fn () => new StreamEnricher(),
            StudentCommandHandler::class => fn (ContainerInterface $c) => new StudentCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            StudentListProjector::class => fn (ContainerInterface $c) => new StudentListProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            SubscriptionCommandHandler::class => fn (ContainerInterface $c) => new SubscriptionCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            SystemCommandHandler::class => fn (ContainerInterface $c) => new SystemCommandHandler(
                $c->get(RepositoryInterface::class),
                $c->get(ProjectionStoreInterface::class),
                $c->get(EventStoreInterface::class),
                $c->get(DispatcherInterface::class),
                $c->get(PdoInterface::class),
            ),
        ];
    }
}
