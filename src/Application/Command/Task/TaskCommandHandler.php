<?php

declare(strict_types=1);

namespace Demo\Application\Command\Task;

use Demo\Application\Command\AbstractCommandHandler;
use Demo\Domain\Sequence\SequenceNumberInterface;
use Demo\Domain\Task\Task;
use Demo\Domain\Task\TaskRepository;

class TaskCommandHandler extends AbstractCommandHandler
{
    private TaskRepository $tasks;

    private SequenceNumberInterface $sequence;

    public function __construct(TaskRepository $tasks, SequenceNumberInterface $sequence)
    {
        $this->tasks = $tasks;
        $this->sequence = $sequence;
    }

    public static function getHandledCommands(): array
    {
        return [
            CompleteTaskCommand::class,
            CreateTaskCommand::class,
            DeleteTaskCommand::class,
            MoveTaskCommand::class,
            RenameTaskCommand::class,
            StartTaskCommand::class,
        ];
    }

    protected function handleCreateTaskCommand(CreateTaskCommand $command): void
    {
        $task = Task::create($command->getTaskId(), $command->getProjectId(), $command->getName(), $this->sequence);
        $this->tasks->store($task);
    }

    protected function handleRenameTaskCommand(RenameTaskCommand $command): void
    {
        $task = $this->tasks->find($command->getTaskId());
        $task->rename($command->getName());
        $this->tasks->store($task);
    }

    protected function handleDeleteTaskCommand(DeleteTaskCommand $command): void
    {
        $task = $this->tasks->find($command->getTaskId());
        $task->delete();
        $this->tasks->store($task);
    }

    protected function handleMoveTaskCommand(MoveTaskCommand $command): void
    {
        $task = $this->tasks->find($command->getTaskId());
        $task->move($command->getProjectId());
        $this->tasks->store($task);
    }

    protected function handleStartTaskCommand(StartTaskCommand $command): void
    {
        $task = $this->tasks->find($command->getTaskId());
        $task->start();
        $this->tasks->store($task);
    }

    protected function handleCompleteTaskCommand(CompleteTaskCommand $command): void
    {
        $task = $this->tasks->find($command->getTaskId());
        $task->complete();
        $this->tasks->store($task);
    }
}
