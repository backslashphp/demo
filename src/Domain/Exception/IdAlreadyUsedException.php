<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class IdAlreadyUsedException extends Exception
{
    public const string MESSAGE = 'ID already used.';
}
