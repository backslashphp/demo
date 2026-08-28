<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\Pdo\PdoInterface;
use PDO;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ViewEventsHandler implements RequestHandlerInterface
{
    private PdoInterface $pdo;

    public function __construct(PdoInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->pdo->query('SELECT e.*, COALESCE((SELECT JSON_GROUP_OBJECT(i.name, i.value) FROM `event_store_identifiers` i WHERE i.event_uid = e.event_uid), "{}") `event_identifiers` FROM `event_store` e ORDER BY e.`sequence` DESC');
        $events = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row;
        }

        return new JsonResponse($events);
    }
}
