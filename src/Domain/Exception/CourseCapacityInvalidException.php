<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class CourseCapacityInvalidException extends Exception
{
    public const string MESSAGE = 'Course capacity must be greater than 0.';
}
