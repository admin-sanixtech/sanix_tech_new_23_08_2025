<?php
// debug.php - Place this in admin/core/approve/
// This will help identify the exact error

// Turn on all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug</title></head><body>";
echo "<h1>Debug Information</h1>";

// Test 1: PHP Version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Test 2: Current working directory
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

// Test 3: File paths
$config_path = __DIR__ . '/../../config/db_connection.php';
echo "<p><strong>Config Path:</strong> $config_path</p>";
echo "<p><strong>Config Exists:</strong> " . (file_exists($config_path) ? 'YES' : 'NO') . "</p>";

// Test 4: Session
session_start();
echo "<p><strong>Session Started:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "</p>";
echo "<p><strong>User ID in Session:</strong> " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "</p>";
echo "<p><strong>User Role:</strong> " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "</p>";

// Test 5: Try to include config
try {
    if (file_exists($config_path)) {
        require_once($config_path);
        echo "<p style='color: green;'><strong>Config included successfully</strong></p>";
        
        // Test connection
        if (isset($conn)) {
            echo "<p style='color: green;'><strong>Database connection variable exists</strong></p>";
            
            if ($conn->connect_error) {
                echo "<p style='color: red;'><strong>Connection Error:</strong> " . $conn->connect_error . "</p>";
            } else {
                echo "<p style='color: green;'><strong>Database connected successfully</strong></p>";
                
                // Test query
                $test_query = "SELECT COUNT(*) as count FROM quiz_questions_pending";
                $result = $conn->query($test_query);
                if ($result) {
                    $count = $result->fetch_assoc()['count'];
                    echo "<p style='color: green;'><strong>Query successful. Total questions:</strong> $count</p>";
                } else {
                    echo "<p style='color: red;'><strong>Query failed:</strong> " . $conn->error . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'><strong>Database connection variable not found</strong></p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Exception:</strong> " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'><strong>Fatal Error:</strong> " . $e->getMessage() . "</p>";
}

// Test 6: Check if required files exist
$required_files = [
    '../admin_menu.php',
    '../admin_navbar.php',
    '../css/admin_styleone.css',
    '../css/approve_user_quiz_questions_styles.css',
    '../js/approve_user_quiz_questions_javascript.js'
];

echo "<h2>Required Files Check:</h2>";
foreach ($required_files as $file) {
    $full_path = __DIR__ . '/' . $file;
    $exists = file_exists($full_path);
    echo "<p><strong>$file:</strong> " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "</p>";
}

echo "</body></html>";
?>