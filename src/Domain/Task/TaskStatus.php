<?php

declare(strict_types=1);

namespace Demo\Domain\Task;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case COMPLETED = 'completed';
}
