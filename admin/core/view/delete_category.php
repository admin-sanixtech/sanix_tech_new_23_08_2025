<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Admins only.');
}

$category_id = $_GET['id'] ?? null;

if ($category_id) {
    $conn->query("DELETE FROM categories WHERE category_id = $category_id");
}

header("Location: add_category.php");
exit();
