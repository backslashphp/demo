<?php

declare(strict_types=1);

namespace Demo\Test;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\EventBus\EventBusInterface;
use Backslash\ProjectionStore\ProjectionStoreInterface;
use Backslash\Scenario\AssertionsTrait;
use Backslash\Scenario\Play;
use Backslash\Scenario\Scenario;
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

        $this->scenario = new Scenario(
            $this->container->get(EventBusInterface::class),
            $this->container->get(DispatcherInterface::class),
            $this->container->get(ProjectionStoreInterface::class),
        );
    }

    protected function newPlay(): Play
    {
        return new Play();
    }
}
