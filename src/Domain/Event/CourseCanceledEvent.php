<?php

declare(strict_types=1);

namespace Demo\Domain\Event;

use Backslash\Domain\EventInterface;
use Backslash\Domain\Identifiers;
use Backslash\Domain\ToArrayTrait;

readonly class CourseCanceledEvent implements EventInterface
{
    use ToArrayTrait;

    public function __construct(
        public string $courseId,
    ) {
    }

    public function getIdentifiers(): Identifiers
    {
        return new Identifiers([
            'courseId' => $this->courseId,
        ]);
    }

}
