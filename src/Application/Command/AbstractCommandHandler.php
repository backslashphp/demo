<?php

declare(strict_types=1);

namespace Demo\Application\Command;

use Backslash\CommandDispatcher\HandleCommandTrait;
use Backslash\CommandDispatcher\HandlerInterface;

abstract class AbstractCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    abstract public static function getHandledCommands(): array;
}
