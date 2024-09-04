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
        $stmt = $this->pdo->prepare("
            SELECT p.name
            FROM roles r
            JOIN role_permissions rp ON r.id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE r.id = :role_id
        ");
        $stmt->execute(['role_id' => $roleId]);
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $permissions;
    }
}
