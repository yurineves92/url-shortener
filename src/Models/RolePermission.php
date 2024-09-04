<?php

namespace App\Models;

use PDO;
use Exception;

class RolePermission
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllRoles(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM roles");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error fetching roles: ' . $e->getMessage());
        }
    }

    public function createRole(string $name): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO roles (name) VALUES (:name)");
            return $stmt->execute(['name' => $name]);
        } catch (Exception $e) {
            throw new Exception('Error creating role: ' . $e->getMessage());
        }
    }

    public function getAllPermissions(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM permissions");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error fetching permissions: ' . $e->getMessage());
        }
    }

    public function createPermission(string $name): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO permissions (name) VALUES (:name)");
            return $stmt->execute(['name' => $name]);
        } catch (Exception $e) {
            throw new Exception('Error creating permission: ' . $e->getMessage());
        }
    }

    public function linkRolePermission(int $roleId, int $permissionId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
            return $stmt->execute([
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ]);
        } catch (Exception $e) {
            throw new Exception('Error linking role to permission: ' . $e->getMessage());
        }
    }

    public function unlinkRolePermission(int $roleId, int $permissionId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id");
            return $stmt->execute([
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ]);
        } catch (Exception $e) {
            throw new Exception('Error unlinking role from permission: ' . $e->getMessage());
        }
    }

    public function getPermissionsForRole(int $roleId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.name
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = :role_id
            ");
            $stmt->execute(['role_id' => $roleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error fetching permissions for role: ' . $e->getMessage());
        }
    }
}
