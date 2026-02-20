<?php

declare(strict_types=1);

namespace Demo\Feature\CourseDefinition\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DefineCourseHandler implements RequestHandlerInterface
{
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->dispatcher->dispatch(new DefineCourseCommand($body['id'], $body['name'], (int) $body['capacity']));

        return new EmptyResponse();
    }
}
