<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\CommandDispatcher\DispatcherInterface;
use Demo\Feature\CourseDefinition\Command\DefineCourseCommand;
use Demo\Feature\CourseSubscription\Command\SubscribeStudentToCourseCommand;
use Demo\Feature\StudentRegistration\Command\RegisterStudentCommand;
use Demo\Feature\Admin\Command\ResetCommand;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DemoHandler implements RequestHandlerInterface
{
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new ResetCommand());

        $this->dispatcher->dispatch(new RegisterStudentCommand('1', 'John'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('2', 'Mary'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('3', 'Bill'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('4', 'James'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('5', 'Lucy'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('6', 'Brad'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('7', 'Kelly'));
        $this->dispatcher->dispatch(new RegisterStudentCommand('8', 'Alice'));

        $this->dispatcher->dispatch(new DefineCourseCommand('1', 'Algebra', 5));
        $this->dispatcher->dispatch(new DefineCourseCommand('2', 'Biology', 4));
        $this->dispatcher->dispatch(new DefineCourseCommand('3', 'Arts', 3));
        $this->dispatcher->dispatch(new DefineCourseCommand('4', 'Physics', 3));
        $this->dispatcher->dispatch(new DefineCourseCommand('5', 'Grammar', 4));

        $this->dispatcher->dispatch(new SubscribeStudentToCourseCommand('1', '2'));
        $this->dispatcher->dispatch(new SubscribeStudentToCourseCommand('2', '3'));
        $this->dispatcher->dispatch(new SubscribeStudentToCourseCommand('2', '4'));
        $this->dispatcher->dispatch(new SubscribeStudentToCourseCommand('3', '4'));

        return new EmptyResponse();
    }
}
