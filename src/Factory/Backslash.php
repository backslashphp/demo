<?php

declare(strict_types=1);

namespace Demo\Factory;

use Backslash\CommandDispatcher\Dispatcher;
use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\CommandDispatcher\HandlerInterface;
use Backslash\CommandDispatcher\MiddlewareInterface as DispatcherMiddlewareInterface;
use Backslash\EventBus\EventBus;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\MiddlewareInterface as EventBusMiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class Backslash implements ContainerInterface
{
    private bool $configured = false;

    private array $resolvers = [];

    private array $cache = [];

    private ContainerInterface $app;

    /** @var DispatcherMiddlewareInterface|callable|string[] */
    private array $dispatcherMiddlewares = [];

    /** @var EventBusMiddlewareInterface|callable|string[] */
    private array $eventBusMiddlewares = [];

    /** @var HandlerInterface|callable|string[] */
    private array $handledCommands = [];

    /** @var EventHandlerInterface|callable|string[] */
    private array $handledEvents = [];

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class ($id) extends RuntimeException implements NotFoundExceptionInterface {
            };
        }
        if (!isset($this->cache[$id])) {
            $resolver = $this->resolvers[$id];
            $this->cache[$id] = $resolver($this);
        }
        return $this->cache[$id];
    }

    public function getDispatcher(): DispatcherInterface
    {
        return $this->get(DispatcherInterface::class);
    }

    public function has(string $id): bool
    {
        if (!$this->configured) {
            throw new RuntimeException('Backslash is not configured.');
        }
        return isset($this->resolvers[$id]);
    }

    public function withDispatcherMiddlewares(DispatcherMiddlewareInterface|callable|string ...$middlewares): self
    {
        $clone = clone $this;
        foreach ($middlewares as $middleware) {
            $clone->dispatcherMiddlewares[] = $middleware;
        }
        return $clone;
    }

    public function withEventBusMiddlewares(EventBusMiddlewareInterface|callable|string ...$middlewares): self
    {
        $clone = clone $this;
        foreach ($middlewares as $middleware) {
            $clone->eventBusMiddlewares[] = $middleware;
        }
        return $clone;
    }

    public function withCommandHandler(HandlerInterface|callable|string $handler, string ...$commandClasses): self
    {
        $clone = clone $this;
        foreach ($commandClasses as $commandClass) {
            $clone->handledCommands[$commandClass] = $handler;
        }
        return $clone;
    }

    public function withEventHandler(EventHandlerInterface|callable|string $handler, string ...$eventClasses): self
    {
        $clone = clone $this;
        foreach ($eventClasses as $eventClass) {
            $clone->handledEvents[$eventClass][] = $handler;
        }
        return $clone;
    }

    public function configure(): self
    {
        if ($this->configured) {
            throw new RuntimeException('Backslash is already configured.');
        }
        $this->configured = true;

        $this->add(DispatcherInterface::class, function (ContainerInterface $container) {
            $dispatcher = new Dispatcher();
            $middlewares = $this->dispatcherMiddlewares;
            /* @TODO: Add ProjectionStoreTransactionCommandDispatcherMiddleware if needed */
            rsort($middlewares);
            foreach ($middlewares as $middleware) {
                $dispatcher->addMiddleware($this->resolve($middleware));
            }
            foreach ($this->handledCommands as $commandClass => $handler) {
                $dispatcher->registerHandler($commandClass, $this->resolve($handler));
            }
            return $dispatcher;
        });

        $this->add(EventBusInterface::class, function (ContainerInterface $container) {
            $eventBus = new EventBus();
            $middlewares = $this->eventBusMiddlewares;
            /* @TODO: Add StreamEnricherEventBusMiddleware if needed */
            rsort($middlewares);
            foreach ($middlewares as $middleware) {
                $eventBus->addMiddleware($this->resolve($middleware));
            }
            foreach ($this->handledEvents as [$eventClass, $handler]) {
                $eventBus->subscribe($eventClass, $this->resolve($handler));
            }
            return $eventBus;
        });

        return $this;
    }

    private function add(string $id, callable $resolver): void
    {
        $this->resolvers[$id] = $resolver;
    }

    private function resolve(object|callable|string $id): mixed
    {
        if (is_callable($id)) {
            return $id();
        } elseif (is_string($id)) {
            return $this->app->get($id);
        }
        return $id;
    }
}
