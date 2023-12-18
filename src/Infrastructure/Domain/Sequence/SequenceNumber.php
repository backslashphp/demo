<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Domain\Sequence;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Domain\Sequence\SequenceNumberInterface;

class SequenceNumber implements SequenceNumberInterface
{
    use GetSequenceTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public function getNextProjectNumber(): int
    {
        return $this->getSequence()->getLastProjectNumber() + 1;
    }

    public function getNextTaskNumber(): int
    {
        return $this->getSequence()->getLastTaskNumber() + 1;
    }
}
