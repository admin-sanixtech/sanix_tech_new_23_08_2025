<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try different possible paths for db_connection.php
$possible_paths = [
    __DIR__ . '/config/db_connection.php',           // Same level as user folder
    __DIR__ . '/../config/db_connection.php',        // One level up
    __DIR__ . '/db_connection.php',                  // In user folder
    'config/db_connection.php',                      // Relative path
    'db_connection.php'                              // Same directory
];

$db_connected = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $db_connected = true;
        break;
    }
}

if (!$db_connected) {
    die("Database connection file not found. Please check the path.");
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
}

// Fetch the user's profile photo from the database
$user_id = $_SESSION['user_id'];
$profilePhoto = 'images/default_profile.jpg'; // Default fallback

try {
    $query = "SELECT photo FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Handle photo path - database already has 'uploads/' prefix for most entries
            if (!empty($row['photo'])) {
                $photo_name = $row['photo'];
                
                // If photo already starts with 'uploads/', use as is
                if (strpos($photo_name, 'uploads/') === 0) {
                    $potential_paths = [
                        $photo_name,                    // uploads/filename.jpg
                        '../' . $photo_name,            // ../uploads/filename.jpg
                        '../../' . $photo_name,         // ../../uploads/filename.jpg
                    ];
                } else {
                    // If no 'uploads/' prefix, add it
                    $potential_paths = [
                        'uploads/' . $photo_name,
                        '../uploads/' . $photo_name,
                        '../../uploads/' . $photo_name,
                    ];
                }
                
                // Check which path actually exists
                foreach ($potential_paths as $path) {
                    if (file_exists($path)) {
                        $profilePhoto = $path;
                        break;
                    }
                }
                
                // If no file found, check if it's accessible via web path
                if ($profilePhoto === 'images/default_profile.jpg') {
                    // Try the paths as web-accessible URLs
                    foreach ($potential_paths as $path) {
                        // Just use the first potential path for web access
                        $profilePhoto = $potential_paths[0];
                        break;
                    }
                }
            }
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Silently use default photo if there's an error
    error_log("Profile photo fetch error: " . $e->getMessage());
}
?>

<!-- Custom CSS for navbar styling -->
<style>
.custom-navbar {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    border-bottom: 2px solid #2196f3 !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    min-height: 60px;
}

.custom-navbar .btn {
    color: #1976d2 !important;
    background-color: rgba(25, 118, 210, 0.1);
    border: 1px solid #1976d2;
    border-radius: 6px;
    padding: 8px 12px;
    transition: all 0.3s ease;
}

.custom-navbar .btn:hover {
    background-color: #1976d2 !important;
    color: white !important;
    transform: translateY(-1px);
}

.custom-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%231976d2' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

.dropdown-menu {
    background-color: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    margin-top: 8px !important;
    min-width: 160px !important;
}

.dropdown-item {
    color: #333333 !important;
    padding: 12px 20px !important;
    transition: all 0.2s ease !important;
    display: block !important;
    text-decoration: none !important;
}

.dropdown-item:hover {
    background-color: #e3f2fd !important;
    color: #1976d2 !important;
}

.dropdown-item i {
    color: #1976d2;
    width: 20px;
    text-align: center;
}

.avatar {
    border: 3px solid #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    transition: transform 0.2s ease;
}

.avatar:hover {
    transform: scale(1.05);
}

.nav-link {
    text-decoration: none !important;
}

.nav-link:focus {
    outline: none !important;
}

/* Debug styles - remove after testing */
.dropdown-menu.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Ensure Bootstrap dropdown works */
.dropdown-toggle::after {
    display: none;
}
</style>

<nav class="navbar navbar-expand-lg px-3 border-bottom custom-navbar">
    <button class="btn" id="sidebar-toggle" type="button" onclick="event.preventDefault(); event.stopPropagation(); toggleSidebar();">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a href="javascript:void(0);" data-bs-toggle="dropdown" class="nav-link dropdown-toggle pe-md-0" role="button" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($profilePhoto); ?>"
                          class="avatar img-fluid rounded-circle"
                          alt="User Profile Picture"
                          onerror="this.src='images/default_profile.jpg'; console.log('Image failed to load:', '<?php echo htmlspecialchars($profilePhoto); ?>');"
                          style="width: 40px; height: 40px; object-fit: cover;" />
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a href="user_profile.php" class="dropdown-item">
                        <i class="fa-solid fa-user me-2"></i>Profile
                    </a></li>
                    <li><a href="settings.php" class="dropdown-item">
                        <i class="fa-solid fa-cog me-2"></i>Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="user_logout.php" class="dropdown-item">
                        <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
                    </a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
// Function to handle sidebar toggle without page refresh
function toggleSidebar() {
    // Add your sidebar toggle logic here
    const sidebar = document.querySelector('.sidebar') || document.querySelector('#sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
    
    // Alternative: trigger a custom event if you have sidebar JS elsewhere
    const event = new CustomEvent('sidebarToggle');
    document.dispatchEvent(event);
}

// Debug: Log the current profile photo path
console.log('Profile photo path:', '<?php echo htmlspecialchars($profilePhoto); ?>');

// Check if image exists by trying to load it
function checkImageExists(imageSrc) {
    const img = new Image();
    img.onload = function() {
        console.log('Image loaded successfully:', imageSrc);
    };
    img.onerror = function() {
        console.log('Image failed to load:', imageSrc);
        // Try alternative paths
        const alternativePaths = [
            'uploads/<?php echo !empty($row["photo"]) ? basename($row["photo"]) : ""; ?>',
            '../uploads/<?php echo !empty($row["photo"]) ? basename($row["photo"]) : ""; ?>',
            '../../uploads/<?php echo !empty($row["photo"]) ? basename($row["photo"]) : ""; ?>'
        ];
        
        for (let altPath of alternativePaths) {
            if (altPath !== 'uploads/' && altPath !== '../uploads/' && altPath !== '../../uploads/') {
                console.log('Trying alternative path:', altPath);
            }
        }
    };
    img.src = imageSrc;
}

// Check current image on page load
document.addEventListener('DOMContentLoaded', function() {
    const profileImg = document.querySelector('.avatar');
    if (profileImg && profileImg.src) {
        checkImageExists(profileImg.src);
    }
});
</script>