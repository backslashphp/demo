<?php

declare(strict_types=1);

namespace Demo\UI\Projection\EnrollmentPeriod;

use Backslash\Projection\ProjectionInterface;

class EnrollmentPeriodProjection implements ProjectionInterface
{
    public const ID = __CLASS__;

    private bool $open = false;

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function open(): void
    {
        $this->open = true;
    }
}
