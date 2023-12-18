<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

use Backslash\Aggregate\AggregateInterface;
use Backslash\Aggregate\AggregateRootTrait;
use Demo\Domain\Sequence\SequenceNumberInterface;
use ErrorException;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class Task implements AggregateInterface
{
    use AggregateRootTrait;

    private string $projectId;

    private string $name;

    private bool $deleted;

    private TaskStatus $status;

    public static function getType(): string
    {
        return 'task';
    }

    public static function create(
        string $taskId,
        string $projectId,
        string $name,
        SequenceNumberInterface $sequence,
    ): self {
        if (!Uuid::isValid($taskId)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid UUID', $taskId));
        }
        $me = new self($taskId);
        $me->apply(new TaskCreatedEvent($sequence->getNextTaskNumber(), $name, $projectId));
        return $me;
    }

    public function rename(string $name): void
    {
        if ($name !== $this->name) {
            $this->apply(new TaskRenamedEvent($this->name, $name));
        }
    }

    public function move(string $projectId): void
    {
        $this->assertTaskIsNotDeleted();
        if ($projectId !== $this->projectId) {
            $this->apply(new TaskMovedEvent($this->projectId, $projectId));
        }
    }

    public function delete(): void
    {
        $this->assertTaskIsNotDeleted();
        $this->apply(new TaskDeletedEvent($this->projectId));
    }

    public function start(): void
    {
        $this->assertTaskIsNotDeleted();
        if ($this->status !== TaskStatus::PENDING) {
            throw new ErrorException('Task must be pending to be started');
        }
        $this->apply(new TaskStartedEvent($this->projectId));
    }

    public function complete(): void
    {
        $this->assertTaskIsNotDeleted();
        if ($this->status !== TaskStatus::STARTED) {
            throw new ErrorException('Task must be started to be completed');
        }
        $this->apply(new TaskCompletedEvent($this->projectId));
    }

    private function applyTaskCreatedEvent(TaskCreatedEvent $event): void
    {
        $this->projectId = $event->getProjectId();
        $this->name = $event->getName();
        $this->status = TaskStatus::PENDING;
        $this->deleted = false;
    }

    private function applyTaskRenamedEvent(TaskRenamedEvent $event): void
    {
        $this->name = $event->getNewName();
    }

    private function applyTaskDeletedEvent(TaskDeletedEvent $event): void
    {
        $this->deleted = true;
    }

    private function applyTaskStartedEvent(TaskStartedEvent $event): void
    {
        $this->status = TaskStatus::STARTED;
    }

    private function applyTaskCompletedEvent(TaskCompletedEvent $event): void
    {
        $this->status = TaskStatus::COMPLETED;
    }

    private function assertTaskIsNotDeleted(): void
    {
        if ($this->deleted) {
            throw new ErrorException('Task is deleted');
        }
    }
}
