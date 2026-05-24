<?php

require_once __DIR__ . '/../config/Database.php';

class Link
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(string $originalUrl, string $shortCode): array
    {
        $sql = "INSERT INTO links (original_url, short_code)
                VALUES (:original_url, :short_code)
                RETURNING id, original_url, short_code";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':original_url' => $originalUrl,
            ':short_code' => $shortCode,
        ]);

        return $stmt->fetch();
    }

    public function all(): array
    {
        $sql = "SELECT id, original_url, short_code FROM links ORDER BY id DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function deleteByValue(string $value): bool
    {
        $sql = "DELETE FROM links
                WHERE original_url = :value OR short_code = :value";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':value' => $value,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function findByShortCode(string $shortCode): ?array
    {
        $sql = "SELECT id, original_url, short_code
                FROM links
                WHERE short_code = :short_code
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':short_code' => $shortCode,
        ]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function shortCodeExists(string $shortCode): bool
    {
        $sql = "SELECT id FROM links WHERE short_code = :short_code LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':short_code' => $shortCode,
        ]);

        return $stmt->fetch() !== false;
    }
}