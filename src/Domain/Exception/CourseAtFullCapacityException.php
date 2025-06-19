<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class CourseAtFullCapacityException extends Exception
{
    public const string MESSAGE = 'Course is at full capacity.';
}
