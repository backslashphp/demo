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
use Backslash\EventNameResolver\EventNameResolver;
use Backslash\EventNameResolver\EventNameResolverInterface;
use Backslash\EventNameResolver\MatchingClassEventNameResolverAdapter;
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
use Demo\Feature\CourseCapacity\Command\ChangeCourseCapacityCommand;
use Demo\Feature\CourseCapacity\Command\CourseCapacityCommandHandler;
use Demo\Feature\CourseCapacity\Event\CourseCapacityChangedEvent;
use Demo\Feature\CourseCapacity\Http\ChangeCourseCapacityHandler as ChangeCourseCapacityHttpHandler;
use Demo\Feature\CourseDefinition\Command\CourseDefinitionCommandHandler;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseDefinition\Event\CourseDefinedEvent;
use Demo\Feature\CourseDefinition\Http\DefineCourseHandler as DefineCourseHttpHandler;
use Demo\Feature\CourseSubscription\Command\CourseSubscriptionCommandHandler;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\CourseSubscription\Command\UnsubscribeStudentFromCourseCommand;
use Demo\Feature\CourseSubscription\Event\StudentSubscribedToCourseEvent;
use Demo\Feature\CourseSubscription\Event\StudentUnsubscribedFromCourseEvent;
use Demo\Feature\CourseSubscription\Http\SubscribeStudentHandler;
use Demo\Feature\CourseSubscription\Http\UnsubscribeStudentHandler;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\StudentRegistration\Command\StudentRegistrationCommandHandler;
use Demo\Feature\StudentRegistration\Event\StudentRegisteredEvent;
use Demo\Feature\StudentRegistration\Http\RegisterStudentHandler as RegisterStudentHttpHandler;
use Demo\Feature\Admin\Command\CreateDatabaseCommand;
use Demo\Feature\Admin\Command\InitializeProjectionsCommand;
use Demo\Feature\Admin\Command\PurgeEventsCommand;
use Demo\Feature\Admin\Command\PurgeProjectionsCommand;
use Demo\Feature\Admin\Command\ResetCommand;
use Demo\Feature\Admin\Command\SystemCommandHandler;
use Demo\Feature\Admin\Http\DemoHandler;
use Demo\Feature\Admin\Http\PurgeProjectionsHandler;
use Demo\Feature\Admin\Http\RebuildProjectionsHandler;
use Demo\Feature\Admin\Http\RebuildProjectionsToHandler;
use Demo\Feature\Admin\Http\ViewEventsHandler;
use Demo\Feature\Admin\Http\ViewProjectionStoreHandler;
use Demo\Feature\CourseView\Http\CourseViewHandler;
use Demo\Feature\CourseView\Projection\CourseProjector;
use Demo\Feature\CourseListView\Http\CourseListViewHandler;
use Demo\Feature\CourseListView\Projection\CourseListProjector;
use Demo\Feature\StudentView\Http\StudentViewHandler;
use Demo\Feature\StudentView\Projection\StudentProjector;
use Demo\Feature\StudentListView\Http\StudentListViewHandler;
use Demo\Feature\StudentListView\Projection\StudentListProjector;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class Container implements ContainerInterface
{
    private const COMMAND_HANDLERS = [
        CourseCapacityCommandHandler::class => [
            ChangeCourseCapacityCommand::class,
        ],
        CourseDefinitionCommandHandler::class => [
            DefineCourseCommand::class,
        ],
        CourseSubscriptionCommandHandler::class => [
            SubscribeStudentToCourseCommand::class,
            UnsubscribeStudentFromCourseCommand::class,
        ],
        StudentRegistrationCommandHandler::class => [
            RegisterStudentCommand::class,
        ],
        SystemCommandHandler::class => [
            CreateDatabaseCommand::class,
            InitializeProjectionsCommand::class,
            PurgeEventsCommand::class,
            PurgeProjectionsCommand::class,
            ResetCommand::class,
        ],
    ];

    private const PROJECTORS = [
        CourseListProjector::class => [
            CourseDefinedEvent::class,
        ],
        CourseProjector::class => [
            CourseCapacityChangedEvent::class,
            CourseDefinedEvent::class,
            StudentSubscribedToCourseEvent::class,
            StudentUnsubscribedFromCourseEvent::class,
        ],
        StudentListProjector::class => [
            StudentRegisteredEvent::class,
        ],
        StudentProjector::class => [
            CourseDefinedEvent::class,
            StudentRegisteredEvent::class,
            StudentSubscribedToCourseEvent::class,
            StudentUnsubscribedFromCourseEvent::class,
        ],
    ];

    private array $cache = [];

    public function __construct()
    {
        $this->configureCommandHandlers();
        $this->configureProjectors();
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
        foreach (self::COMMAND_HANDLERS as $handlerClass => $commandClasses) {
            foreach ($commandClasses as $commandClass) {
                $dispatcher->registerHandler($commandClass, new HandlerProxy(fn () => $this->get($handlerClass)));
            }
        }
    }

    private function configureProjectors(): void
    {
        /** @var EventBusInterface $eventBus */
        $eventBus = $this->get(EventBusInterface::class);
        foreach (self::PROJECTORS as $projectorClass => $eventClasses) {
            foreach ($eventClasses as $eventClass) {
                $eventBus->subscribe($eventClass, new EventHandlerProxy(fn () => $this->get($projectorClass)));
            }
        }
    }

    private function getServices(): array
    {
        return [
            // Command handlers
            CourseCapacityCommandHandler::class => fn (ContainerInterface $c) => new CourseCapacityCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            CourseDefinitionCommandHandler::class => fn (ContainerInterface $c) => new CourseDefinitionCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            CourseSubscriptionCommandHandler::class => fn (ContainerInterface $c) => new CourseSubscriptionCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            StudentRegistrationCommandHandler::class => fn (ContainerInterface $c) => new StudentRegistrationCommandHandler(
                $c->get(RepositoryInterface::class),
            ),
            SystemCommandHandler::class => fn (ContainerInterface $c) => new SystemCommandHandler(
                $c->get(RepositoryInterface::class),
                $c->get(ProjectionStoreInterface::class),
                $c->get(EventStoreInterface::class),
                $c->get(DispatcherInterface::class),
                $c->get(PdoInterface::class),
            ),

            // Projectors
            CourseListProjector::class => fn (ContainerInterface $c) => new CourseListProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            CourseProjector::class => fn (ContainerInterface $c) => new CourseProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            StudentListProjector::class => fn (ContainerInterface $c) => new StudentListProjector(
                $c->get(ProjectionStoreInterface::class),
            ),
            StudentProjector::class => fn (ContainerInterface $c) => new StudentProjector(
                $c->get(ProjectionStoreInterface::class),
            ),

            // HTTP handlers — views
            StudentListViewHandler::class => fn (ContainerInterface $c) => new StudentListViewHandler(
                $c->get(ProjectionStoreInterface::class),
            ),
            StudentViewHandler::class => fn (ContainerInterface $c) => new StudentViewHandler(
                $c->get(ProjectionStoreInterface::class),
            ),
            CourseListViewHandler::class => fn (ContainerInterface $c) => new CourseListViewHandler(
                $c->get(ProjectionStoreInterface::class),
            ),
            CourseViewHandler::class => fn (ContainerInterface $c) => new CourseViewHandler(
                $c->get(ProjectionStoreInterface::class),
            ),

            // HTTP handlers — commands
            RegisterStudentHttpHandler::class => fn (ContainerInterface $c) => new RegisterStudentHttpHandler(
                $c->get(DispatcherInterface::class),
            ),
            DefineCourseHttpHandler::class => fn (ContainerInterface $c) => new DefineCourseHttpHandler(
                $c->get(DispatcherInterface::class),
            ),
            ChangeCourseCapacityHttpHandler::class => fn (ContainerInterface $c) => new ChangeCourseCapacityHttpHandler(
                $c->get(DispatcherInterface::class),
            ),
            SubscribeStudentHandler::class => fn (ContainerInterface $c) => new SubscribeStudentHandler(
                $c->get(DispatcherInterface::class),
            ),
            UnsubscribeStudentHandler::class => fn (ContainerInterface $c) => new UnsubscribeStudentHandler(
                $c->get(DispatcherInterface::class),
            ),

            // HTTP handlers — admin
            DemoHandler::class => fn (ContainerInterface $c) => new DemoHandler(
                $c->get(DispatcherInterface::class),
            ),
            RebuildProjectionsHandler::class => fn (ContainerInterface $c) => new RebuildProjectionsHandler(
                $c->get(DispatcherInterface::class),
                $c->get(EventStoreInterface::class),
                $c->get(EventBusInterface::class),
                $c->get(StreamEnricherInterface::class),
                $c->get(ProjectionStoreInterface::class),
            ),
            RebuildProjectionsToHandler::class => fn (ContainerInterface $c) => new RebuildProjectionsToHandler(
                $c->get(DispatcherInterface::class),
                $c->get(EventStoreInterface::class),
                $c->get(EventBusInterface::class),
                $c->get(StreamEnricherInterface::class),
                $c->get(ProjectionStoreInterface::class),
            ),
            PurgeProjectionsHandler::class => fn (ContainerInterface $c) => new PurgeProjectionsHandler(
                $c->get(DispatcherInterface::class),
            ),
            ViewEventsHandler::class => fn (ContainerInterface $c) => new ViewEventsHandler(
                $c->get(PdoInterface::class),
            ),
            ViewProjectionStoreHandler::class => fn (ContainerInterface $c) => new ViewProjectionStoreHandler(
                $c->get(PdoInterface::class),
            ),

            // Infrastructure
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
            EventNameResolverInterface::class => fn () => new EventNameResolver(
                new MatchingClassEventNameResolverAdapter(),
            ),
            EventStoreInterface::class => function (ContainerInterface $c) {
                $eventNameResolver = $c->get(EventNameResolverInterface::class);
                $store = new EventStore(
                    new PdoEventStoreAdapter(
                        $c->get(PdoInterface::class),
                        new PdoEventStoreConfig(),
                        $eventNameResolver,
                        new Serializer(new JsonEventSerializer($eventNameResolver)),
                        new Serializer(new JsonIdentifiersSerializer()),
                        new Serializer(new JsonMetadataSerializer()),
                        fn () => Uuid::uuid4()->toString(),
                    ),
                );
                $store->addMiddleware(new StreamEnricherEventStoreMiddleware($c->get(StreamEnricherInterface::class)));
                return $store;
            },
            PdoInterface::class => function () {
                $dsn = (getenv('TESTING') || getenv('APP_SHARED'))
                    ? 'sqlite::memory:'
                    : 'sqlite:data/demo.sqlite';
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
            SharedModeMiddleware::class => fn (ContainerInterface $c) => new SharedModeMiddleware($c->get(PdoInterface::class)),
            StreamEnricherInterface::class => fn () => new StreamEnricher(),
        ];
    }
}
