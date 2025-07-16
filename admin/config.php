<?php
// Start the session (make sure it happens before any output)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db_connection.php';
?>
