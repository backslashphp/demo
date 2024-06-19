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
    ['#', 'UID', 'CLASS', 'PAYLOAD', 'IDENTIFIERS', 'METADATA', 'TIMESTAMP'],
];
$query = $pdo->query('SELECT * FROM `event_store` ORDER BY `sequence` ASC');
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = [
        $row['sequence'],
        $row['event_uid'],
        $row['event_class'],
        $row['event_payload'],
        $row['event_identifiers'],
        $row['event_metadata'],
        $row['event_time'],
    ];
}

echo new Table($rows);
