<?php
//db_connection.php
$servername = "localhost";
$username   = "sanixazs";
$password   = "Kri1Lin2@#$%";
$dbname     = "sanixazs_main_db";

try {
    // PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Also create a MySQLi connection if some code depends on $conn
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("MySQLi connection failed: " . $conn->connect_error);
    }
    
    // Set charset for MySQLi as well
    $conn->set_charset("utf8mb4");

} catch (PDOException $e) {
    die("PDO DB connection failed: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>