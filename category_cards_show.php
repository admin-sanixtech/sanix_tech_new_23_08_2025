<?php
require_once 'db_connection.php';
include_once 'config.php';

$query = "SELECT * FROM categories";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
    <!-- Cleaned up duplicate CSS links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h2 class="mb-4 text-center">Learning Zone</h2>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $imagePath = BASE_URL . '/uploads/' . htmlspecialchars($row['category_image']);
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $imagePath; ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($row['category_name']); ?>"
                             onerror="this.onerror=null;this.src='https://via.placeholder.com/400x200?text=Image+Not+Found';"
                             style="height: 200px; object-fit: cover;">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['category_name']); ?></h5>
                            <a href="category_content_show.php?id=<?php echo (int)$row['category_id']; ?>" class="btn btn-primary">View Posts</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
