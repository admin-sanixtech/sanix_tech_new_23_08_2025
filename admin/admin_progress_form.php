<?php
// Include DB connection
include 'db_connection.php';

// Assume session is active and user_id is stored
session_start();
$user_id = $_SESSION['user_id']; // Change this if you're storing user info differently
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Progress Submission</title>
</head>
<body>
    <h2>Submit Your Work Progress</h2>
    <form method="post" action="submit_user_progress.php">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <label>Topic:</label><br>
        <input type="text" name="topic" required><br><br>

        <label>Duration (in minutes):</label><br>
        <input type="number" name="duration_minutes" required><br><br>

        <label>Progress (%):</label><br>
        <input type="number" name="progress_percent" min="0" max="100" required><br><br>

        <label>Description of Work Done:</label><br>
        <textarea name="work_description" rows="4" cols="50" required></textarea><br><br>

        <label>Date of Work:</label><br>
        <input type="date" name="date_of_work" required><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="Blocked">Blocked</option>
        </select><br><br>

        <label>Remarks (optional):</label><br>
        <textarea name="remarks" rows="2" cols="50"></textarea><br><br>

        <input type="submit" value="Submit Progress">
    </form>
</body>
</html>
