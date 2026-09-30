<?php

namespace App\Models;

use App\Database;
use PDO;

class User {
    public static function all(): array {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT u.*, r.nombre as rol_nombre 
            FROM users u 
            JOIN roles r ON u.rol_id = r.id 
            ORDER BY u.id DESC
        ");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre as rol_nombre 
            FROM users u 
            JOIN roles r ON u.rol_id = r.id 
            WHERE u.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password_hash, rol_id, estado, created_at)
            VALUES (:username, :email, :password_hash, :rol_id, :estado, NOW())
        ");
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'rol_id' => $data['rol_id'],
            'estado' => $data['estado'] ?? 'Activo'
        ]);
        return (int)$db->lastInsertId();
    }
}
