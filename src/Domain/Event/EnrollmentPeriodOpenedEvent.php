<?php

declare(strict_types=1);

namespace Demo\Domain\Event;

use Backslash\Domain\EventInterface;
use Backslash\Domain\Identifiers;
use Backslash\Domain\ToArrayTrait;

readonly class EnrollmentPeriodOpenedEvent implements EventInterface
{
    use ToArrayTrait;

    public function getIdentifiers(): Identifiers
    {
        return new Identifiers([]);
    }
}
