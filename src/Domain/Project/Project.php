<?php

declare(strict_types=1);

namespace Demo\Domain\Project;

use Backslash\Aggregate\AggregateInterface;
use Backslash\Aggregate\AggregateRootTrait;
use Demo\Domain\Sequence\SequenceNumberInterface;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class Project implements AggregateInterface
{
    use AggregateRootTrait;

    private string $name;

    private ProjectStatus $status;

    public static function getType(): string
    {
        return 'project';
    }

    public static function create(string $projectId, string $name, SequenceNumberInterface $sequence): self
    {
        if (!Uuid::isValid($projectId)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid UUID', $projectId));
        }
        $name = trim($name);
        if (empty($name)) {
            $name = 'Untitled project';
        }
        $me = new self($projectId);
        $me->apply(new ProjectCreatedEvent($sequence->getNextProjectNumber(), $name));
        $me->apply(new ProjectStatusChangedEvent(ProjectStatus::PENDING->value));
        return $me;
    }

    public function rename(string $name): void
    {
        if ($name !== $this->name) {
            $this->apply(new ProjectRenamedEvent($this->name, $name));
        }
    }

    public function changeStatus(ProjectStatus $status): void
    {
        if ($this->status !== $status) {
            $this->apply(new ProjectStatusChangedEvent($status->value));
        }
    }

    private function applyProjectCreatedEvent(ProjectCreatedEvent $event): void
    {
        $this->name = $event->getName();
        $this->status = ProjectStatus::PENDING;
    }

    private function applyProjectStatusChangedEvent(ProjectStatusChangedEvent $event): void
    {
        $this->status = ProjectStatus::from($event->getStatus());
    }
}
