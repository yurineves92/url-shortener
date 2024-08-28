<?php

namespace App\Models;

use PDO;

class UrlLog
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function logAccess(string $uuid, int $urlId, ?string $meta = null): void
    {
        $sql = 'INSERT INTO urls_logs (uuid, url_id, meta, created_at) VALUES (:uuid, :url_id, :meta, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uuid' => $uuid,
            ':url_id' => $urlId,
            ':meta' => $meta
        ]);
    }
}
