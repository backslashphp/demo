<?php

declare(strict_types=1);

use Backslash\Pdo\PdoInterface;
use Demo\UI\Table;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../bootstrap.php';
/** @var PdoInterface $pdo */
$pdo = $container->get(PdoInterface::class);

$rows = [
    ['#', 'AGGREGATE', 'TYPE', 'VERSION', 'CLASS', 'PAYLOAD', 'METADATA', 'TIMESTAMP'],
];
$query = $pdo->query('SELECT * FROM `event_store` ORDER BY `sequence` ASC');
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = [
        $row['sequence'],
        $row['aggregate_id'],
        $row['aggregate_type'],
        $row['aggregate_version'],
        $row['event_class'],
        $row['event_payload'],
        $row['event_metadata'],
        $row['event_time'],
    ];
}

echo new Table($rows);
