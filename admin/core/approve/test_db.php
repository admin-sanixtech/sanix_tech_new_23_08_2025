<?php
// test_db.php - Place this in admin/core/approve/
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if config file exists
$config_path = __DIR__ . '/../config/db_connection.php';
echo "<p><strong>Config file path:</strong> $config_path</p>";

if (!file_exists($config_path)) {
    echo "<p style='color: red;'>❌ Config file does not exist!</p>";
    echo "<p>Expected path: $config_path</p>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Config file exists</p>";
}

// Test 2: Try to include the config
try {
    require_once($config_path);
    echo "<p style='color: green;'>✅ Config file included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error including config: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Check if connection variable exists
if (!isset($conn)) {
    echo "<p style='color: red;'>❌ Database connection variable \$conn not found!</p>";
    exit;
}

// Test 4: Test the connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
}

// Test 5: Check if table exists
$table_check = $conn->query("SHOW TABLES LIKE 'quiz_questions_pending'");
if ($table_check->num_rows > 0) {
    echo "<p style='color: green;'>✅ Table 'quiz_questions_pending' exists</p>";
} else {
    echo "<p style='color: red;'>❌ Table 'quiz_questions_pending' does not exist!</p>";
    
    // Show available tables
    $tables_result = $conn->query("SHOW TABLES");
    echo "<p><strong>Available tables:</strong></p><ul>";
    while ($row = $tables_result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    exit;
}

// Test 6: Check table structure
echo "<h3>Table Structure:</h3>";
$structure = $conn->query("DESCRIBE quiz_questions_pending");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $structure->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Test 7: Count pending questions
$count_result = $conn->query("SELECT COUNT(*) as count FROM quiz_questions_pending WHERE status = 'pending'");
$count = $count_result->fetch_assoc()['count'];
echo "<p><strong>Pending questions count:</strong> $count</p>";

if ($count > 0) {
    echo "<p style='color: green;'>✅ Found pending questions in database</p>";
    
    // Show sample data
    $sample = $conn->query("SELECT pending_id, question_text, status, created_at FROM quiz_questions_pending WHERE status = 'pending' LIMIT 3");
    echo "<h3>Sample Data:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Question (first 50 chars)</th><th>Status</th><th>Created</th></tr>";
    while ($row = $sample->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['pending_id'] . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['question_text'], 0, 50)) . "...</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No pending questions found</p>";
}

echo "<p><strong>Test completed successfully!</strong></p>";
?>