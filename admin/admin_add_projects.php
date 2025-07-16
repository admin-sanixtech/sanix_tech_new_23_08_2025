<?php
include 'config.php';  // Include session_start() and db_connection.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch categories for the dropdown
$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $projectName = mysqli_real_escape_string($conn, $_POST['project_name']);
    $projectDescription = mysqli_real_escape_string($conn, $_POST['project_description']);
    $categoryId = $_POST['category_id'];
    $subcategoryId = $_POST['subcategory_id'];

    // Handle file upload for ZIP file
    $zipFilePath = '';
    if (isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] == 0) {
        $targetDir = '/public_html/uploads/project_zip_files/'; // Directory where the ZIP files will be stored
        $fileName = basename($_FILES['zip_file']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is a ZIP file
        if ($fileType === 'zip') {
            if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $targetFile)) {
                $zipFilePath = $targetFile; // Save the file path
            } else {
                echo "Error uploading ZIP file.";
            }
        } else {
            echo "Invalid file type. Only ZIP files are allowed.";
        }
    }

    // Insert project data into the database
    $insertQuery = "INSERT INTO projects (project_name, project_description, category_id, subcategory_id, zip_file_path) 
                    VALUES ('$projectName', '$projectDescription', '$categoryId', '$subcategoryId', '$zipFilePath')";

    if (mysqli_query($conn, $insertQuery)) {
        echo "Project added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <link rel="stylesheet" href="css/admin_add_projects.css" />
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="form-container">
                    <h2>Add New Project</h2>
                    <form action="admin_add_projects.php" method="POST" enctype="multipart/form-data">
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
                        </select>

                        <!-- ZIP File Upload -->
                        <label for="zip_file">Upload ZIP File</label>
                        <input type="file" id="zip_file" name="zip_file" accept=".zip">

                        <!-- Submit Button -->
                        <button type="submit">Add Project</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#category_id').on('change', function () {
            const categoryId = $(this).val();

            // Clear the subcategory dropdown
            $('#subcategory_id').html('<option value="">Loading...</option>');

            // Send an AJAX request to fetch subcategories
            $.ajax({
                url: 'fetch_subcategories.php',
                type: 'POST',
                data: { category_id: categoryId },
                success: function (data) {
                    // Populate the subcategory dropdown with the response
                    $('#subcategory_id').html(data);
                },
                error: function () {
                    alert('Error fetching subcategories');
                }
            });
        });
    });
</script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
