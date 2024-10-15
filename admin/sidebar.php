<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Admin access only.";
    exit;
}
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="subcategory_management.php">Subcategory Management</a></li>
            <li><a href="quiz_management.php">Quiz Management</a></li>
            <li><a href="add_question.php">Add New Question</a></li>
            <li><a href="view_results.php">View Results</a></li>
            <li><a href="user_management.php">User Management</a></li>
        </ul>
    </nav>
</aside>
