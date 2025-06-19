<?php

declare(strict_types=1);

namespace Demo\Domain\Exception;

use Exception;

class StudentAlreadySubscribedToCourseException extends Exception
{
    public const string MESSAGE = 'Student is already subscribed to course.';
}
