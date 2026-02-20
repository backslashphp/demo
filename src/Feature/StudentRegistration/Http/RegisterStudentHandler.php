<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegisterStudentHandler implements RequestHandlerInterface
{
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->dispatcher->dispatch(new RegisterStudentCommand($body['id'], $body['name']));

        return new EmptyResponse();
    }
}
