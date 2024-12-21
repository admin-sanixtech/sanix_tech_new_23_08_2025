<?php
session_start(); // Start session to check if admin is logged in

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Admin access only.";
    exit;
}

// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission for services
    if (isset($_POST['service_submit'])) {
        $service_name = $_POST['service_name'];
        $description = $_POST['service_description'];
        $sql = "INSERT INTO services (service_name, description) VALUES ('$service_name', '$description')";
        if ($conn->query($sql)) {
            echo "Service added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Handle form submission for courses
    if (isset($_POST['course_submit'])) {
        $course_name = $_POST['course_name'];
        $description = $_POST['course_description'];
        $sql = "INSERT INTO courses (course_name, description) VALUES ('$course_name', '$description')";
        if ($conn->query($sql)) {
            echo "Course added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tooltip { position: absolute; background: rgba(0,0,0,0.7); color: white; padding: 5px; pointer-events: none; }
        .hover-lift { transition: transform 0.3s ease-in-out; }
        .hover-lift:hover { transform: translateY(-5px); }
    </style>
</head>
<body
<?php
        include 'header.php';
        include 'sidebar.php';
    ?>

    <h2>Add New Service</h2>
    <form action="" method="POST">
        <label for="service_name">Service Name:</label>
        <input type="text" name="service_name" required><br>

        <label for="service_description">Description:</label>
        <textarea name="service_description" required></textarea><br>

        <button type="submit" name="service_submit">Add Service</button>
    </form>

    <h2>Add New Course</h2>
    <form action="" method="POST">
        <label for="course_name">Course Name:</label>
        <input type="text" name="course_name" required><br>

        <label for="course_description">Description:</label>
        <textarea name="course_description" required></textarea><br>

        <button type="submit" name="course_submit">Add Course</button>
    </form>
</body>
</html>
