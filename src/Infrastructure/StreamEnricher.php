<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\Aggregate\Stream;
use Backslash\StreamEnricher\StreamEnricherInterface;

class StreamEnricher implements StreamEnricherInterface
{
    private bool $enabled = true;

    public function enrich(Stream $stream): Stream
    {
        if (!$this->enabled) {
            return $stream;
        }
        $newStream = new Stream($stream->getAggregateId(), $stream->getAggregateType());
        foreach ($stream->getRecordedEvents() as $recordedEvent) {
            $metadata = $recordedEvent->getMetadata();
            $newMetadata = $metadata->with('correlation_id', CorrelationId::get());
            $newRecordedEvent = $recordedEvent->withMetadata($newMetadata);
            $newStream = $newStream->withRecordedEvent($newRecordedEvent);
        }
        return $newStream;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}
