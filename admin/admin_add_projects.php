
<?php
session_start();
include 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch categories and subcategories from the database for the form
$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);

$subcategoryQuery = "SELECT * FROM subcategories";  // Assuming you have a subcategories table
$subcategoryResult = mysqli_query($conn, $subcategoryQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $projectName = mysqli_real_escape_string($conn, $_POST['project_name']);
    $projectDescription = mysqli_real_escape_string($conn, $_POST['project_description']);
    $categoryId = $_POST['category_id'];
    $subcategoryId = $_POST['subcategory_id'];

    // Handle file upload for QR code
    $qrCodePath = '';
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $targetDir = 'uploads/qr_codes/'; // Directory where the QR codes will be stored
        $fileName = basename($_FILES['qr_code']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is an image (optional)
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['qr_code']['tmp_name'], $targetFile)) {
                $qrCodePath = $targetFile; // Save the file path
            } else {
                echo "Error uploading QR code image.";
            }
        } else {
            echo "Invalid file type for QR code. Only JPG, PNG, and GIF are allowed.";
        }
    }

    // Insert project data into the database
    $insertQuery = "INSERT INTO projects (project_name, project_description, category_id, subcategory_id, qr_code) 
                    VALUES ('$projectName', '$projectDescription', '$categoryId', '$subcategoryId', '$qrCodePath')";

    if (mysqli_query($conn, $insertQuery)) {
        echo "Project added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <style>
        label {
            display: block;
            margin: 8px 0;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Project</h2>
    <form action="add_projects.php" method="POST" enctype="multipart/form-data">
        <!-- Project Name -->
        <label for="project_name">Project Name</label>
        <input type="text" id="project_name" name="project_name" required>

        <!-- Project Description -->
        <label for="project_description">Project Description</label>
        <textarea id="project_description" name="project_description" rows="4" required></textarea>

        <!-- Category -->
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($row = mysqli_fetch_assoc($categoryResult)) : ?>
                <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Subcategory -->
        <label for="subcategory_id">Subcategory</label>
        <select id="subcategory_id" name="subcategory_id" required>
            <option value="">Select Subcategory</option>
            <?php while ($row = mysqli_fetch_assoc($subcategoryResult)) : ?>
                <option value="<?= $row['subcategory_id'] ?>"><?= $row['subcategory_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <!-- QR Code Upload -->
        <label for="qr_code">QR Code</label>
        <input type="file" id="qr_code" name="qr_code" accept="image/*">

        <!-- Submit Button -->
        <button type="submit">Add Project</button>
    </form>
</div>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
