<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $role = $_POST['role'];
    $email_to = $_POST['email_to'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO job_post (title, role, email_to, description, location, created_by, is_approved) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sssssi", $title, $role, $email_to, $description, $location, $created_by);
    $stmt->execute();
    echo "<div class='alert alert-success'>Job submitted for admin approval.</div>";
}
?>

<!-- User HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Submit Job - User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body class="p-4">
    <h3>Submit Job for Approval</h3>
    <form method="POST">
        <input class="form-control mb-2" name="title" placeholder="Job Title" required />
        <input class="form-control mb-2" name="role" placeholder="Role (e.g., Data Engineer)" required />
        <input class="form-control mb-2" type="email" name="email_to" placeholder="Email to" required />
        <input class="form-control mb-2" name="location" placeholder="Location" />
        <textarea class="form-control mb-2" name="description" placeholder="Job Description"></textarea>
        <button class="btn btn-primary">Submit</button>
    </form>
</body>
</html>
