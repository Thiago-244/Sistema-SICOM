<?php

namespace App\Helpers;

use App\Database;
use PDO;

class Auth {
    public static function login(string $username, string $password): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre as rol_nombre 
            FROM users u
            JOIN roles r ON u.rol_id = r.id
            WHERE (u.username = :user OR u.email = :user) AND u.estado = 'Activo'
            LIMIT 1
        ");
        $stmt->execute(['user' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Actualizar último acceso
            $upd = $db->prepare("UPDATE users SET ultimo_acceso = NOW() WHERE id = :id");
            $upd->execute(['id' => $user['id']]);

            // Cargar datos de comunicador si aplica
            $comunicadorId = null;
            if ($user['rol_id'] == 2) {
                $cStmt = $db->prepare("SELECT id FROM comunicadores WHERE user_id = :uid LIMIT 1");
                $cStmt->execute(['uid' => $user['id']]);
                $comunicador = $cStmt->fetch();
                $comunicadorId = $comunicador['id'] ?? null;
            }

            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('email', $user['email']);
            Session::set('rol_id', (int)$user['rol_id']);
            Session::set('rol_nombre', $user['rol_nombre']);
            Session::set('comunicador_id', $comunicadorId);

            AuditLogger::log('AUTH_LOGIN', 'users', $user['id'], null, ['user' => $user['username']]);
            return true;
        }

        return false;
    }

    public static function user(): ?array {
        if (!Session::has('user_id')) {
            return null;
        }
        return [
            'id' => Session::get('user_id'),
            'username' => Session::get('username'),
            'email' => Session::get('email'),
            'rol_id' => Session::get('rol_id'),
            'rol_nombre' => Session::get('rol_nombre'),
            'comunicador_id' => Session::get('comunicador_id')
        ];
    }

    public static function check(): bool {
        return Session::has('user_id');
    }

    public static function isRole(int|array $roles): bool {
        $user = self::user();
        if (!$user) return false;

        if (is_array($roles)) {
            return in_array($user['rol_id'], $roles);
        }
        return $user['rol_id'] === $roles;
    }

    public static function logout(): void {
        $user = self::user();
        if ($user) {
            AuditLogger::log('AUTH_LOGOUT', 'users', $user['id']);
        }
        Session::remove('user_id');
        Session::remove('username');
        Session::remove('email');
        Session::remove('rol_id');
        Session::remove('rol_nombre');
        Session::remove('comunicador_id');
        session_destroy();
    }
}
