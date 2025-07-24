<?php
ob_start();  // Start output buffering to prevent "headers already sent" errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Only start the session if not already started
}
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the user's profile photo from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT photo FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profilePhoto = file_exists('uploads/' . $row['photo']) ? 'uploads/' . $row['photo'] : 'images/default_profile.jpg';
    } else {
        $profilePhoto = 'images/default_profile.jpg'; // Default image if no photo is uploaded
    }
    $stmt->close();
} else {
    die("Database query failed: " . $conn->error);
}

$conn->close();
ob_end_flush();  // Flush the output buffer (optional)
?>

<nav class="navbar navbar-expand-lg px-3 border-bottom">
    <button class="btn" id="sidebar-toggle" type="button"><span class="navbar-toggler-icon"></span></button>
    <div class="navbar-collapse navbar">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a href="#" class="nav-icon pe-md-0" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($profilePhoto); ?>" class="avatar img-fluid rounded" alt="Profile Picture of User <?php echo htmlspecialchars($user_id); ?>" />
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="admin_profile.php" class="dropdown-item">Profile</a>
                    <a href="settings.php" class="dropdown-item">Settings</a>
                    <a href="admin_logout.php" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
