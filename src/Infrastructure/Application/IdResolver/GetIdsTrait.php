<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Application\IdResolver;

use Backslash\ProjectionStore\ProjectionNotFoundException;

trait GetIdsTrait
{
    private function getIds(): IdsProjection
    {
        try {
            /** @var IdsProjection $p */
            $p = $this->projections->find(IdsProjection::ID, IdsProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new IdsProjection();
        }
        return $p;
    }
}
