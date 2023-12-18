<?php

declare(strict_types=1);

namespace Demo\Infrastructure\Domain\Sequence;

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\Application\AbstractEventHandler;
use Demo\Domain\Project\ProjectCreatedEvent;
use Demo\Domain\Task\TaskCreatedEvent;

class SequenceNumberProjector extends AbstractEventHandler
{
    use GetSequenceTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProjectCreatedEvent::class,
            TaskCreatedEvent::class,
        ];
    }

    protected function handleProjectCreatedEvent(string $projectId, ProjectCreatedEvent $event): void
    {
        $sequence = $this->getSequence();
        $sequence->setLastProjectNumber($event->getNumber());
        $this->projections->store($sequence);
    }

    protected function handleTaskCreatedEvent(string $taskId, TaskCreatedEvent $event): void
    {
        $sequence = $this->getSequence();
        $sequence->setLastTaskNumber($event->getNumber());
        $this->projections->store($sequence);
    }
}
