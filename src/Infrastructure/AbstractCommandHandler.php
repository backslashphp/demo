<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\CommandDispatcher\HandleCommandTrait;
use Backslash\CommandDispatcher\HandlerInterface;
use Backslash\Repository\RepositoryInterface;

abstract class AbstractCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
