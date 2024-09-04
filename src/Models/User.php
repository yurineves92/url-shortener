<?php

namespace App\Models;

use PDO;
use Exception;

class User
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserByEmail(string $email): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, email, password, role_id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log('Error fetching user: ' . $e->getMessage());
            return null;
        }
    }

    public function createUser(string $email, string $password, int $role_id): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (email, password, active, role_id) VALUES (:email, :password, :active, :role_id)");
            return $stmt->execute([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'active' => 1,
                'role_id' => $role_id
            ]);
        } catch (Exception $e) {
            error_log('Erro ao buscar o usuário: ' . $e->getMessage());
            return false;
        }
    }

    public function userExists(string $email): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error checking if user exists: ' . $e->getMessage());
            return false;
        }
    }
}
