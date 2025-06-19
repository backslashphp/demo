<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class CourseNotDefinedException extends Exception
{
    public const string MESSAGE = 'Course is not defined.';
}
