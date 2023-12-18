<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
