<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\Domain\RecordedEventStream;
use Backslash\StreamEnricher\StreamEnricherInterface;

class StreamEnricher implements StreamEnricherInterface
{
    private bool $enabled = true;

    public function enrich(RecordedEventStream $stream): RecordedEventStream
    {
        if (!$this->enabled) {
            return $stream;
        }
        $newStream = new RecordedEventStream();
        foreach ($stream->getRecordedEvents() as $recordedEvent) {
            $metadata = $recordedEvent->getMetadata();
            $newMetadata = $metadata->with('correlation_id', CorrelationId::get());
            $newRecordedEvent = $recordedEvent->withMetadata($newMetadata);
            $newStream = $newStream->withRecordedEvents($newRecordedEvent);
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
