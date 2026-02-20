<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\Admin\Command\PurgeProjectionsCommand;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PurgeProjectionsHandler implements RequestHandlerInterface
{
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new PurgeProjectionsCommand());

        return new EmptyResponse();
    }
}
