<?php
/**
 * Diagnostic Debug File
 * Upload to Hostinger and visit: https://jsmkj.space/Ipark/debug.php
 */

echo "<h2>iPark Diagnostic Report</h2>";

// 1. Check PHP Version
echo "<h3>1. PHP Version:</h3>";
echo "PHP " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4', '>=')) {
    echo "✅ PHP version is compatible<br>";
} else {
    echo "❌ PHP version too old (need 7.4+)<br>";
}

// 2. Check Required Extensions
echo "<h3>2. Required PHP Extensions:</h3>";
$extensions = ['mysqli', 'json', 'openssl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext installed<br>";
    } else {
        echo "❌ $ext NOT installed<br>";
    }
}

// 3. Check File Permissions
echo "<h3>3. File/Directory Permissions:</h3>";
$paths = [
    __DIR__ . '/config',
    __DIR__ . '/config/db.php',
    __DIR__ . '/includes',
    __DIR__ . '/web',
    __DIR__ . '/database'
];
foreach ($paths as $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "✅ $path (perms: $perms)<br>";
    } else {
        echo "❌ $path NOT FOUND<br>";
    }
}

// 4. Try Database Connection
echo "<h3>4. Database Connection Test:</h3>";
try {
    require_once(__DIR__ . '/config/db.php');
    
    if ($conn->connect_error) {
        echo "❌ Connection failed: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Connected to database successfully!<br>";
        
        // Check if tables exist
        $result = $conn->query("SHOW TABLES");
        echo "Tables found: " . $result->num_rows . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// 5. Display Config Constants
echo "<h3>5. Configuration Constants:</h3>";
if (defined('DB_HOST')) {
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "DB_USER: " . DB_USER . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "SITE_URL: " . SITE_URL . "<br>";
} else {
    echo "❌ Config constants not defined<br>";
}

// 6. Check index.php issues
echo "<h3>6. Checking index.php:</h3>";
$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    echo "✅ index.php exists<br>";
    
    // Try to parse it for syntax errors
    $content = file_get_contents($index_file);
    if (preg_match('/<\?php/', $content)) {
        echo "✅ index.php has PHP opening tag<br>";
    } else {
        echo "❌ index.php missing PHP opening tag<br>";
    }
} else {
    echo "❌ index.php not found<br>";
}

echo "<h3>Done!</h3>";
echo "<p>If you see errors above, share the details with us.</p>";
?>
