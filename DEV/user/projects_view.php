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

// Fetch categories from the database
$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);

// Fetch projects based on the selected category
$projects = [];
if (isset($_GET['category_id'])) {
    $categoryId = $_GET['category_id'];

    // Ensure the category_id is numeric
    if (!is_numeric($categoryId)) {
        die('Invalid category ID');
    }
    // Fetch projects for the selected category
    $projectQuery = "SELECT * FROM projects WHERE category_id = '$categoryId'";  
    $projectResult = mysqli_query($conn, $projectQuery);

    if (!$projectResult) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Check if any projects are found
    if (mysqli_num_rows($projectResult) > 0) {
        while ($row = mysqli_fetch_assoc($projectResult)) {
            $projects[] = [
                'id' => $row['id'],
                'name' => $row['project_name'],
                'short_description' => substr($row['project_description'], 0, 100) . '...',
                'full_description' => $row['project_description'],
                'category_id' => $row['category_id']
            ];
        }
    } else {
        echo 'No projects found for this category.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        button {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Select Category</h2>
<form method="GET" action="">
    <select name="category_id" onchange="this.form.submit()">
        <option value="">Select Category</option>
        <?php while ($row = mysqli_fetch_assoc($categoryResult)) : ?>
            <option value="<?= $row['category_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $row['category_id']) ? 'selected' : '' ?>>
                <?= $row['category_name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if (isset($_GET['category_id']) && !empty($projects)) : ?>
    <h2>Projects</h2>
    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Short Description</th>
                <th>Full Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project) : ?>
                <tr>
                    <td><?= $project['name'] ?></td>
                    <td><?= $project['short_description'] ?></td>
                    <td><a href="project_detail.php?project_id=<?= $project['id'] ?>"><button>More</button></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif (isset($_GET['category_id'])): ?>
    <p>No projects found for this category.</p>
<?php endif; ?>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
