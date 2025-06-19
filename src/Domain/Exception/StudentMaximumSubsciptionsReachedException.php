<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class StudentMaximumSubsciptionsReachedException extends Exception
{
    public const string MESSAGE = 'Student has reached maximum course subscriptions.';
}
