<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventBus\EventBusInterface;
use Backslash\EventStore\EventStoreInterface;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\Repository\RepositoryInterface;
use Backslash\Scenario\AssertionsTrait;
use Backslash\Scenario\Scenario;
use Demo\Feature\Admin\Command\ResetCommand;
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

        $this->container = require __DIR__ . '/../../bootstrap.php';

        /** @var DispatcherInterface $dispatcher */
        $dispatcher = $this->container->get(DispatcherInterface::class);
        $dispatcher->dispatch(new ResetCommand());

        $this->scenario = new Scenario(
            $this->container->get(EventBusInterface::class),
            $this->container->get(DispatcherInterface::class),
            $this->container->get(ProjectionStoreInterface::class),
            $this->container->get(EventStoreInterface::class),
            $this->container->get(RepositoryInterface::class),
        );
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
