<?php
require __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$databaseUrl = $_ENV['DATABASE_URL'];
$components = parse_url($databaseUrl);
$dbName = ltrim($components['path'], '/');
// Crear la conexión
$conn = new mysqli($components['host'], $components['user'], $components['pass'], $dbName);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
