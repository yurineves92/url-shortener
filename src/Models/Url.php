<?php

namespace App\Models;

use PDO;

class Url
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createUrl(string $longUrl, string $shortUrlPath, string $type = 'RANDOM', ?string $meta = null): ?string
    {
        $uuid = $this->generateUuid();
        $economyRate = ceil(100 - ((strlen($shortUrlPath) * 100) / strlen($longUrl)));
        
        $sql = 'INSERT INTO urls (uuid, long_url, short_url_path, type, economy_rate, meta, created_at) VALUES (:uuid, :long_url, :short_url_path, :type, :economy_rate, :meta, NOW())';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uuid' => $uuid,
            ':long_url' => $longUrl,
            ':short_url_path' => $shortUrlPath,
            ':type' => $type,
            ':economy_rate' => $economyRate,
            ':meta' => $meta
        ]);
        
        return $uuid;
    }

    public function getUrlByShortPath(string $shortUrlPath): ?array
    {
        $sql = 'SELECT * FROM urls WHERE short_url_path = :short_url_path LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':short_url_path' => $shortUrlPath]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result !== false ? $result : null;
    }

    private function generateUuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
