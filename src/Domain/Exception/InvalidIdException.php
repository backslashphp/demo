<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class InvalidIdException extends Exception
{
    public const string MESSAGE = 'ID must be a number.';
}
