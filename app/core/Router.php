<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function delete(string $path, callable $callback): void
    {
        $this->routes['DELETE'][$path] = $callback;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        if ($method === 'GET' && preg_match('/^\/([a-zA-Z0-9]{6,10})$/', $path, $matches)) {
            if (isset($this->routes['GET']['/{shortCode}'])) {
                call_user_func($this->routes['GET']['/{shortCode}'], $matches[1]);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'error',
            'message' => 'Route not found'
        ], JSON_PRETTY_PRINT);
    }
}