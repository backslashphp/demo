<?php

declare(strict_types=1);

namespace Demo\Feature\StudentRegistration\Event;

use Backslash\Event\EventInterface;
use Backslash\Event\Identifiers;
use Backslash\Event\ToArrayTrait;

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
