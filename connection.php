<?php
// db/connection.php
// Shared PDO connection. Include this at the top of every API file.

define("DB_PATH", __DIR__ . "/app.db");

try {
    $db = new PDO("sqlite:" . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enable WAL mode for better concurrent read performance
    $db->exec("PRAGMA journal_mode=WAL;");
    $db->exec("PRAGMA foreign_keys=ON;");
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}
