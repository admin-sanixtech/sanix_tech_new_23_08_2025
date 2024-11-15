<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profilePhoto = $row['photo'] ? 'uploads/' . $row['photo'] : 'images/default_profile.jpg';
} else {
    $profilePhoto = 'images/default_profile.jpg'; // Default image if no photo is uploaded
}

$stmt->close();
$conn->close();
?>

<nav class="navbar navbar-expand px-3 border-bottom">
    <button class="btn" id="sidebar-toggle" type="button"><span class="navbar-toggler-icon"></span> </button>
    <div class="navbar-collapse navbar">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                    <img src="<?php echo htmlspecialchars($profilePhoto); ?>" class="avatar img-fluid rounded" alt="User Profile Picture" />
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="user_profile.php" class="dropdown-item">Profile</a>
                    <a href="settings.php" class="dropdown-item">Setting</a>
                    <a href="user_logout.php" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
