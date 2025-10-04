<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\Scenario\AssertionsTrait;
use Backslash\Scenario\Play;
use Backslash\Scenario\Scenario;
use Demo\Application\Command\System\ResetCommand;
use Demo\Infrastructure\ExitOnErrorCommandDispatcherMiddleware;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;

class TestCase extends PHPUnitTestCase
{
    use AssertionsTrait;

    protected Scenario $scenario;

    private ContainerInterface $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = require __DIR__ . '/../bootstrap.php';

        /** @var ExitOnErrorCommandDispatcherMiddleware $exitOnError */
        $exitOnError = $this->container->get(ExitOnErrorCommandDispatcherMiddleware::class);
        $exitOnError->enable(false);

        /** @var DispatcherInterface $dispatcher */
        $dispatcher = $this->container->get(DispatcherInterface::class);
        $dispatcher->dispatch(new ResetCommand());

        $this->scenario = new Scenario(
            $this->container->get(EventBusInterface::class),
            $this->container->get(DispatcherInterface::class),
            $this->container->get(ProjectionStoreInterface::class),
            $this->container->get(EventStoreInterface::class),
        );
    }

    protected function newPlay(): Play
    {
        return new Play();
    }
}
