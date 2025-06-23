<?php
// Test database connection and tables
require_once 'config/database.php';

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    // Test database connection
    $pdo = connectDb();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Get database name
    $db_name = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "<p>Connected to database: <strong>$db_name</strong></p>";
    
    // Check tables
    echo "<h3>Database Tables:</h3>";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tables found in the database.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check required files
echo "<h3>Required Files:</h3>";
$required_files = [
    'config/database.php',
    'templates/header.php',
    'templates/footer.php',
    'result.php'
];

echo "<ul>";
foreach ($required_files as $file) {
    $exists = file_exists($file);
    $status = $exists ? "✅" : "❌";
    echo "<li>$status $file - " . ($exists ? 'Found' : 'Missing') . "</li>";
}
echo "</ul>";

// Check PHP info
echo "<h3>PHP Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check required extensions
$required_extensions = ['pdo_mysql', 'mbstring', 'json'];
echo "<h3>Required PHP Extensions:</h3>";
echo "<ul>";
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "✅" : "❌";
    echo "<li>$status $ext - " . ($loaded ? 'Loaded' : 'Not loaded') . "</li>";
}
echo "</ul>";

// Check file permissions
$writable_dirs = ['reports', 'templates', 'config'];
echo "<h3>Directory Permissions:</h3>";
echo "<ul>";
foreach ($writable_dirs as $dir) {
    $exists = file_exists($dir);
    $writable = $exists ? is_writable($dir) : false;
    $status = $exists ? ($writable ? "✅" : "⚠️") : "❌";
    echo "<li>$status $dir - " . 
         ($exists ? ($writable ? 'Writable' : 'Not writable') : 'Does not exist') . 
         "</li>";
}
echo "</ul>";
