<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\EventStore\Query\QueryInterface;
use Backslash\StreamPublishingInspection\Inspector;
use Demo\Feature\Admin\Command\InitializeProjectionsCommand;
use Demo\Feature\Admin\Command\PurgeProjectionsCommand;

trait RebuildProjectionsTrait
{
    private function rebuild(?QueryInterface $query = null): void
    {
        $this->dispatcher->dispatch(new PurgeProjectionsCommand());
        $this->dispatcher->dispatch(new InitializeProjectionsCommand());
        $this->enricher->disable();

        $this->eventStore->inspect(new Inspector($this->eventBus, $query));

        $this->projections->commit();
        $this->enricher->enable();
    }
}
