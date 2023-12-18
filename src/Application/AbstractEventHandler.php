<?php

declare(strict_types=1);

namespace Demo\Application;

use Backslash\EventBus\EventHandlerInterface;
use Backslash\EventBus\EventHandlerTrait;

abstract class AbstractEventHandler implements EventHandlerInterface
{
    use EventHandlerTrait;

    abstract public static function getSubscribedEvents(): array;
}
