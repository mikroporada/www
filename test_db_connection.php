<?php
// Test database connection
require_once 'config/database.php';

try {
    $pdo = connectDb();
    echo "Database connection successful!<br>";
    
    // Test if users table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    echo "Users table exists: " . (count($tables) > 0 ? 'Yes' : 'No') . "<br>";
    
    // Test if user_interactions table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'user_interactions'")->fetchAll();
    echo "User_interactions table exists: " . (count($tables) > 0 ? 'Yes' : 'No') . "<br>";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}

// Test if required files exist
$required_files = [
    'config/database.php',
    'templates/header.php',
    'templates/footer.php'
];

echo "<br>Checking required files:<br>";
foreach ($required_files as $file) {
    echo "$file: " . (file_exists($file) ? 'Exists' : 'MISSING') . "<br>";
}

// Test if reports directory exists and is writable
$reports_dir = 'reports';
echo "<br>Reports directory: " . (is_dir($reports_dir) ? 'Exists' : 'MISSING') . "<br>";
if (is_dir($reports_dir)) {
    echo "Reports directory is " . (is_writable($reports_dir) ? 'writable' : 'NOT writable') . "<br>";
}

// Check PHP version
echo "<br>PHP Version: " . phpversion() . "<br>";

// Check for required PHP extensions
$required_extensions = ['pdo_mysql', 'mbstring', 'json'];
echo "<br>Checking required PHP extensions:<br>";
foreach ($required_extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? 'Loaded' : 'MISSING') . "<br>";
}
