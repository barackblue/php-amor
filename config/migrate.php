<?php
require_once __DIR__ . '/config.php'; // DB + settings

// Colored output functions
function colorRed($msg) { return "\033[31m$msg\033[0m"; }
function colorYellow($msg) { return "\033[33m$msg\033[0m"; }
function colorGreen($msg) { return "\033[32m$msg\033[0m"; }

// Load models
$modelsFile = __DIR__ . '/models.php';
if (!file_exists($modelsFile)) {
    die(colorRed("Error: models.php file not found at $modelsFile\n"));
}

$models = require $modelsFile;

// Directory to store executed migrations
$migrationDir = __DIR__ . '/migrations';
if (!file_exists($migrationDir)) mkdir($migrationDir, 0755, true);

// Load history of migrations
$historyFile = $migrationDir . '/.history.json';
$history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];

// Loop through each model/table
foreach ($models as $table => $columns) {

    echo colorGreen("Processing table: $table\n");

    // Check if table exists
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result === false) {
        die(colorRed("Error checking table $table: " . $mysqli->error . "\n"));
    }
    $tableExists = $result && $result->num_rows > 0;

    $sqlStatements = [];

    if (!$tableExists) {
        echo colorYellow(" - Table does not exist. Creating...\n");
        $cols = [];
        foreach ($columns as $col => $type) {
            $cols[] = "$col $type";
        }
        $sqlStatements[] = "CREATE TABLE $table (" . implode(", ", $cols) . ")";
    } else {
        // Table exists → check for column changes
        $result = $mysqli->query("SHOW COLUMNS FROM `$table`");
        if ($result === false) {
            die(colorRed("Error reading columns for $table: " . $mysqli->error . "\n"));
        }

        $existingCols = [];
        while ($row = $result->fetch_assoc()) {
            $existingCols[] = $row['Field'];
        }

        // Add new columns
        foreach ($columns as $col => $type) {
            if (!in_array($col, $existingCols)) {
                $sqlStatements[] = "ALTER TABLE $table ADD COLUMN $col $type";
                echo colorGreen(" - Adding column: $col\n");
            }
        }

        // Drop removed columns (destructive – optional toggle)
        foreach ($existingCols as $col) {
            if (!isset($columns[$col])) {
                if (!empty($settings['allowDestructiveMigrations'])) {
                    $sqlStatements[] = "ALTER TABLE $table DROP COLUMN $col";
                    echo colorYellow(" - Dropping column: $col\n");
                } else {
                    echo colorYellow(" - Column $col exists but is missing in models.php (destructive disabled)\n");
                }
            }
        }
    }

    // Run SQL statements
    foreach ($sqlStatements as $sql) {
        if (!$mysqli->query($sql)) {
            if ($settings['debug']) {
                die(colorRed("Migration failed: " . $mysqli->error . "\nSQL: $sql\n"));
            } else {
                die(colorRed("Critical migration failure\n"));
            }
        }
    }

    // Record executed migration
    if (!in_array($table, $history)) {
        $history[] = $table;
    }
}

// Save migration history
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));

echo colorGreen("✅ Migrations completed successfully!\n");
