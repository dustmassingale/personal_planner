<?php
function get_db() {
  $dbFile = __DIR__ . '/personal_planner.db';
  $dsn = "sqlite:" . $dbFile;
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Ensure schema exists
  $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='values'");
  if ($stmt->fetch() === false) {
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
  }

  return $pdo;
}
