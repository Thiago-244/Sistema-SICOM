<?php

namespace App\Helpers;

class Session {
    public static function start(): void {
        if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function flash(string $key, mixed $value = null): mixed {
        self::start();
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        } else {
            $val = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
    }

    public static function csrfToken(): string {
        self::start();
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(string $token): bool {
        self::start();
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}
