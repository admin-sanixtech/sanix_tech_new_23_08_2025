<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

// Fetch project details based on the project ID from URL
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $projectId = $_GET['project_id'];

    // Fetch project details
    $projectQuery = "SELECT * FROM projects WHERE id = '$projectId'";
    $projectResult = mysqli_query($conn, $projectQuery);

    if (!$projectResult) {
        die("Query failed: " . mysqli_error($conn));
    }

    $project = mysqli_fetch_assoc($projectResult);
    
    // Fetch category details for this project
    $categoryQuery = "SELECT category_name FROM categories WHERE category_id = '" . $project['category_id'] . "'";
    $categoryResult = mysqli_query($conn, $categoryQuery);
    $category = mysqli_fetch_assoc($categoryResult);
} else {
    echo "Invalid project ID.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
        }

        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .qr-code {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Project: <?= $project['project_name'] ?></h2>
    <p><strong>Category:</strong> <?= $category['category_name'] ?></p>
    <p><strong>Description:</strong></p>
    <p><?= $project['project_description'] ?></p>

    <!-- Download Button -->
    <button class="btn" id="downloadBtn">Download</button>

    <!-- QR Code Section -->
    <div class="qr-code" id="qrCode" style="display: none;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode('https://yourwebsite.com/download/' . $project['id']) ?>&size=150x150" alt="QR Code">
        <p>Scan to download</p>
    </div>
</div>

<script>
    document.getElementById('downloadBtn').addEventListener('click', function() {
        // Toggle QR Code visibility
        const qrCode = document.getElementById('qrCode');
        qrCode.style.display = (qrCode.style.display === 'none') ? 'block' : 'none';
    });
</script>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
