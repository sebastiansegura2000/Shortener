<?php

require_once __DIR__ . '/../models/Link.php';
require_once __DIR__ . '/../config/Database.php';

class LinkController
{
    private Link $linkModel;
    private string $appUrl;

    public function __construct()
    {
        $this->linkModel = new Link();

        $config = require __DIR__ . '/../config/config.php';
        $this->appUrl = rtrim($config['app_url'], '/');
    }

    public function shorten(): void
    {
        $data = $this->getJsonInput();

        if (!isset($data['url']) || empty(trim($data['url']))) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'URL is required'
            ], 400);
            return;
        }

        $url = trim($data['url']);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'Invalid URL'
            ], 400);
            return;
        }

        $shortCode = $this->generateUniqueShortCode();

        $link = $this->linkModel->create($url, $shortCode);

        $this->jsonResponse([
            'original_url' => $link['original_url'],
            'short_url' => $this->appUrl . '/' . $link['short_code']
        ], 201);
    }

    public function list(): void
    {
        $links = $this->linkModel->all();

        $response = array_map(function ($link) {
            return [
                'id' => $link['id'],
                'original_url' => $link['original_url'],
                'short_code' => $link['short_code'],
                'short_url' => $this->appUrl . '/' . $link['short_code'],
            ];
        }, $links);

        $this->jsonResponse($response);
    }

    public function delete(): void
    {
        $data = $this->getJsonInput();

        if (!isset($data['value']) || empty(trim($data['value']))) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'Value is required'
            ], 400);
            return;
        }

        $value = trim($data['value']);

        $deleted = $this->linkModel->deleteByValue($value);

        if (!$deleted) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'Link not found'
            ], 404);
            return;
        }

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'Link deleted successfully'
        ]);
    }

    public function health(): void
    {
        $connected = Database::checkConnection();

        if ($connected) {
            $this->jsonResponse([
                'status' => 'success',
                'database' => 'connected'
            ]);
            return;
        }

        $this->jsonResponse([
            'status' => 'error',
            'database' => 'disconnected'
        ], 500);
    }

    public function redirect(string $shortCode): void
    {
        $link = $this->linkModel->findByShortCode($shortCode);

        if (!$link) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'Short link not found'
            ], 404);
            return;
        }

        header('Location: ' . $link['original_url'], true, 302);
        exit;
    }

    private function generateUniqueShortCode(int $length = 6): string
    {
        do {
            $shortCode = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
        } while ($this->linkModel->shortCodeExists($shortCode));

        return $shortCode;
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');

        $data = json_decode($input, true);

        return is_array($data) ? $data : [];
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}