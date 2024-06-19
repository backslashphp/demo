<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CommandDispatcher\DispatcherInterface;
use Backslash\CommandDispatcher\MiddlewareInterface;
use Throwable;

class ExitOnErrorCommandDispatcherMiddleware implements MiddlewareInterface
{
    private bool $enabled = true;

    public function dispatch(object $command, DispatcherInterface $next): void
    {
        if (!$this->enabled) {
            $next->dispatch($command);
            return;
        }

        try {
            $next->dispatch($command);
        } catch (Throwable $t) {
            echo 'ERROR: ' . $t->getMessage() . PHP_EOL;
            exit(-1);
        }
    }

    public function enable(bool $enable): void
    {
        $this->enabled = $enable;
    }
}
