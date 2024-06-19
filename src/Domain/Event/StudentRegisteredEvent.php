<?php

declare(strict_types=1);

namespace Demo\Domain\Event;

use Backslash\Domain\EventInterface;
use Backslash\Domain\Identifiers;
use Backslash\Domain\ToArrayTrait;

readonly class StudentRegisteredEvent implements EventInterface
{
    use ToArrayTrait;

    public function __construct(
        public string $studentId,
        public string $name,
    ) {
    }

    public function getIdentifiers(): Identifiers
    {
        return new Identifiers([
            'studentId' => $this->studentId,
        ]);
    }
}
