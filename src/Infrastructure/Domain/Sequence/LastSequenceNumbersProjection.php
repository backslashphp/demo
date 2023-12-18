<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Domain\Sequence;

use Backslash\Projection\ProjectionInterface;

class LastSequenceNumbersProjection implements ProjectionInterface
{
    public const ID = __CLASS__;

    private int $lastProjectNumber = 0;

    private int $lastTaskNumber = 0;

    public function getId(): string
    {
        return self::ID;
    }

    public function getLastProjectNumber(): int
    {
        return $this->lastProjectNumber;
    }

    public function setLastProjectNumber(int $number): void
    {
        $this->lastProjectNumber = $number;
    }

    public function getLastTaskNumber(): int
    {
        return $this->lastTaskNumber;
    }

    public function setLastTaskNumber(int $number): void
    {
        $this->lastTaskNumber = $number;
    }
}
