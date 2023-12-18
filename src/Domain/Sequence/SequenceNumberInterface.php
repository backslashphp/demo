<?php

declare(strict_types=1);

namespace Demo\Domain\Sequence;

interface SequenceNumberInterface
{
    public function getNextProjectNumber(): int;

    public function getNextTaskNumber(): int;
}
