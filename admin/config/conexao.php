<?php
declare(strict_types=1);

function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'zephyr.proxy.rlwy.net';
    $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '46100';
    $database = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'menustock';
    $user = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
    $password = getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: 'ojuEPvwwMfxxPVxEbtBZrEFVKfSvwfrr';

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4;connect_timeout=5";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        http_response_code(500);
        exit('Falha ao conectar ao banco de dados: ' . $exception->getMessage());
    }

    return $pdo;
}
