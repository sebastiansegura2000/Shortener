<?php

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/config.php';

            $db = $config['database'];

            $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['name']}";

            self::$connection = new PDO($dsn, $db['user'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$connection;
    }

    public static function checkConnection(): bool
    {
        try {
            self::connect();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}