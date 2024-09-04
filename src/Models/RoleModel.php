<?php

namespace App\Models;

use PDO;

class RoleModel
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPermissionsByRoleId(int $roleId): array
    {
        $stmt = $this->pdo->prepare('SELECT permission_id FROM role_permissions WHERE role_id = ?');
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
