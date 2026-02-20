<?php

declare(strict_types=1);

namespace Demo\Feature\CourseView\Http;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseView\Projection\CourseProjection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class CourseViewHandler implements RequestHandlerInterface
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = RouteContext::fromRequest($request)->getRoute()->getArgument('id');

        /** @var CourseProjection $course */
        $course = $this->projections->find(CourseProjection::id($id), CourseProjection::class);

        return new JsonResponse($course);
    }
}
