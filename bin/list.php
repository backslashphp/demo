<?php

declare(strict_types=1);

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\UI\Projection\ProjectList\ProjectListProjection;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var ProjectionStoreInterface $projections */
$projections = $container->get(ProjectionStoreInterface::class);
/** @var ProjectListProjection $list */
$list = $projections->find(ProjectListProjection::ID, ProjectListProjection::class);

echo count($list) ? $list : 'PROJECT LIST IS EMPTY' . PHP_EOL;
