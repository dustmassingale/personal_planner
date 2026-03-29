<?php
// db/bootstrap.php
// Run once from CLI: php db/bootstrap.php
// Creates the SQLite database and applies schema.

$dbPath = __DIR__ . "/app.db";
$sql    = file_get_contents(__DIR__ . "/schema.sql");

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec($sql);
    echo "✅ Database initialised at $dbPath\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
