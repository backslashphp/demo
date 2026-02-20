<?php

declare(strict_types=1);

namespace Demo\Feature\CourseListView\Http;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\CourseView\Projection\CourseProjection;
use Demo\Feature\CourseListView\Projection\CourseListProjection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CourseListViewHandler implements RequestHandlerInterface
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var CourseListProjection $list */
        $list = $this->projections->find(CourseListProjection::ID, CourseListProjection::class);

        $courses = array_map(
            fn (string $id) => $this->projections->find(CourseProjection::id($id), CourseProjection::class),
            $list->getCourseIds(),
        );

        return new JsonResponse($courses);
    }
}
