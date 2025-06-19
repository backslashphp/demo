<?php

declare(strict_types=1);

use Backslash\ProjectionStore\ProjectionStoreInterface;
use Demo\UI\Projection\CourseList\CourseListProjection;
use Demo\UI\Projection\StudentList\StudentListProjection;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var ProjectionStoreInterface $projections */
$projections = $container->get(ProjectionStoreInterface::class);
/** @var StudentListProjection $students */
$students = $projections->find(StudentListProjection::ID, StudentListProjection::class);
/** @var CourseListProjection $courses */
$courses = $projections->find(CourseListProjection::ID, CourseListProjection::class);

echo $students . PHP_EOL;
echo $courses . PHP_EOL;
