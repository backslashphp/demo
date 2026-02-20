<?php

declare(strict_types=1);

namespace Demo\Feature\Admin\Http;

use Backslash\Pdo\PdoInterface;
use PDO;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ViewProjectionStoreHandler implements RequestHandlerInterface
{
    private PdoInterface $pdo;

    public function __construct(PdoInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->pdo->query('SELECT * FROM `projection_store`');
        $rows = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $p = unserialize($row['projection_payload']);
            $row['projection_payload'] = json_encode($p);
            $rows[] = $row;
        }

        return new JsonResponse($rows);
    }
}
