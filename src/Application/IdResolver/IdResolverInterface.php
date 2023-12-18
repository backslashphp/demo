<?php

declare(strict_types=1);

namespace Demo\Application\IdResolver;

interface IdResolverInterface
{
    /** @throws IdNotFoundException */
    public function resolveProjectId(int $fromNumber): string;

    /** @throws IdNotFoundException */
    public function resolveTaskId(int $fromNumber): string;
}
