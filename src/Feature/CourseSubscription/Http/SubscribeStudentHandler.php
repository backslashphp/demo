<?php

declare(strict_types=1);

namespace Demo\Feature\CourseSubscription\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SubscribeStudentHandler implements RequestHandlerInterface
{
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->dispatcher->dispatch(new SubscribeStudentToCourseCommand($body['studentId'], $body['courseId']));

        return new EmptyResponse();
    }
}
