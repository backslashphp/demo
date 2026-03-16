<?php

declare(strict_types=1);

namespace Demo\Infrastructure;

use Backslash\Pdo\PdoInterface;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SharedModeMiddleware implements MiddlewareInterface
{
    private PdoInterface $pdo;

    public function __construct(PdoInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->restoreFromSession();
        $response = $handler->handle($request);
        $this->saveToSession();

        return $response;
    }

    private function restoreFromSession(): void
    {
        foreach (['event_store', 'projection_store'] as $table) {
            if (empty($_SESSION[$table])) {
                continue;
            }

            $columns = implode(', ', array_keys($_SESSION[$table][0]));
            $placeholders = implode(', ', array_fill(0, count($_SESSION[$table][0]), '?'));
            $stmt = $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");

            foreach ($_SESSION[$table] as $row) {
                $stmt->execute(array_values($row));
            }
        }
    }

    private function saveToSession(): void
    {
        foreach (['event_store', 'projection_store'] as $table) {
            $stmt = $this->pdo->query("SELECT * FROM $table");
            $_SESSION[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
