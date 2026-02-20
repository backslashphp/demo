<?php

declare(strict_types=1);

namespace Demo\Feature\StudentView\Http;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\StudentView\Projection\StudentProjection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class StudentViewHandler implements RequestHandlerInterface
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = RouteContext::fromRequest($request)->getRoute()->getArgument('id');

        /** @var StudentProjection $student */
        $student = $this->projections->find(StudentProjection::id($id), StudentProjection::class);

        return new JsonResponse($student);
    }
}
