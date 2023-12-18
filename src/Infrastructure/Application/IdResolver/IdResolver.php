<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Application\IdResolver;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\IdResolver\IdNotFoundException;
use Demo\Application\IdResolver\IdResolverInterface;

class IdResolver implements IdResolverInterface
{
    use GetIdsTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function resolveProjectId(int $fromNumber): string
    {
        $id = $this->getIds()->getProjectId($fromNumber);
        if (!$id) {
            throw new IdNotFoundException();
        }
        return $id;
    }

    public function resolveTaskId(int $fromNumber): string
    {
        $id = $this->getIds()->getTaskId($fromNumber);
        if (!$id) {
            throw new IdNotFoundException();
        }
        return $id;
    }
}
