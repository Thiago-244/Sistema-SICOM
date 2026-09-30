<?php

namespace App;

use App\Helpers\Session;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        Session::start();

        $rawUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rawurldecode($rawUri);
        $method = $_SERVER['REQUEST_METHOD'];

        // Extraer la ruta relativa quitando el prefijo del directorio local en XAMPP (/Sistema SICOM/public)
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $path = substr($uri, strlen($scriptDir));
        } else {
            $path = $uri;
        }

        $path = '/' . trim($path, '/');

        // Limpiar parámetros query extra
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }

        if (isset($this->routes[$method][$path])) {
            [$class, $action] = $this->routes[$method][$path];
            $controller = new $class();
            $controller->$action();
        } else {
            // Manejar 404
            http_response_code(404);
            require __DIR__ . '/Views/errors/404.php';
        }
    }
}
