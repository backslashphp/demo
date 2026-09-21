<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ProjectionStoreCommitMiddleware implements MiddlewareInterface
{
    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (Throwable $t) {
            $this->projections->rollback();
            throw $t;
        }

        $this->projections->commit();

        return $response;
    }
}
