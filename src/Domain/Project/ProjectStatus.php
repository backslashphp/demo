<?php

declare(strict_types=1);

namespace Demo\Domain\Project;

enum ProjectStatus: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case COMPLETED = 'completed';
}
