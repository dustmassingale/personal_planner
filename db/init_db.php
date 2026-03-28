<?php
$dbFile = __DIR__ . '/personal_planner.db';
$schemaFile = __DIR__ . '/schema.sql';
echo "Initializing database...\n";
if (!file_exists($schemaFile)) {
    echo "Schema file not found: $schemaFile\n";
    exit(1);
}
$sql = file_get_contents($schemaFile);
// Try PDO first
if (extension_loaded('pdo_sqlite')) {
    try {
        $dsn = 'sqlite:' . $dbFile;
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec($sql);
        echo "Initialized database via PDO (sqlite): $dbFile\n";
        exit(0);
    } catch (Exception $e) {
        echo "PDO initialization failed: " . $e->getMessage() . "\n";
    }
}

// Fallback to SQLite3 extension
if (class_exists('SQLite3')) {
    try {
        $db = new SQLite3($dbFile);
        $db->exec($sql);
        $db->close();
        echo "Initialized database via SQLite3: $dbFile\n";
        exit(0);
    } catch (Exception $e) {
        echo "SQLite3 initialization failed: " . $e->getMessage() . "\n";
    }
}

echo "No suitable SQLite driver found (pdo_sqlite or SQLite3). Please enable one in php.ini.\n";
exit(1);
