<?php

declare(strict_types=1);

namespace Demo\Feature\StudentListView\Http;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Feature\StudentView\Projection\StudentProjection;
use Demo\Feature\StudentListView\Projection\StudentListProjection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StudentListViewHandler implements RequestHandlerInterface
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var StudentListProjection $list */
        $list = $this->projections->find(StudentListProjection::ID, StudentListProjection::class);

        $students = array_map(
            fn (string $id) => $this->projections->find(StudentProjection::id($id), StudentProjection::class),
            $list->getStudentIds(),
        );

        return new JsonResponse($students);
    }
}
