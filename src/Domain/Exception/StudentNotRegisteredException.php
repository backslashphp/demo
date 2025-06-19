<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class StudentNotRegisteredException extends Exception
{
    public const string MESSAGE = 'Student is not registered.';
}
