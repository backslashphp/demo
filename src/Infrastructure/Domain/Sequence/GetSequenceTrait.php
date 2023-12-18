<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Domain\Sequence;

use Backslash\ProjectionStore\ProjectionNotFoundException;

trait GetSequenceTrait
{
    private function getSequence(): LastSequenceNumbersProjection
    {
        try {
            /** @var LastSequenceNumbersProjection $p */
            $p = $this->projections->find(LastSequenceNumbersProjection::ID, LastSequenceNumbersProjection::class);
        } catch (ProjectionNotFoundException) {
            $p = new LastSequenceNumbersProjection();
        }
        return $p;
    }
}
