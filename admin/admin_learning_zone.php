<?php 
require_once 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all categories with their images
$query = "SELECT category_id, category_name, category_image FROM categories ORDER BY category_name";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Define absolute paths for images
$baseURL = 'https://www.sanixtech.in/';
$imageBasePath = $baseURL . 'uploads/';
$fallbackImage = $baseURL . 'assets/no-image.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies - Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
    <style>
        .category-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: 1px solid #dee2e6;
            height: 100%;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .card-img-container {
            height: 150px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: calc(0.375rem - 1px) calc(0.375rem - 1px) 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card-img-top {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            padding: 0.5rem;
        }
        .no-image-placeholder {
            width: 80px;
            height: 80px;
            background-color: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 12px;
            text-align: center;
        }
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-center mb-4">Choose Your Course</h2>
                <p class="text-center text-muted">Select a category to explore our courses</p>
            </div>
        </div>
        
        <div class="row" id="categoriesContainer">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                        <div class="card category-card" 
                             data-category-id="<?php echo htmlspecialchars($row['category_id']); ?>"
                             onclick="navigateToCategory(<?php echo htmlspecialchars($row['category_id']); ?>)">
                            <div class="card-img-container">
                                <?php if (!empty($row['category_image'])): ?>
                                    <img src="<?php echo $imageBasePath . htmlspecialchars($row['category_image']); ?>"
                                         class="card-img-top"
                                         alt="<?php echo htmlspecialchars($row['category_name']); ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="no-image-placeholder" style="display: none;">
                                        <i class="fas fa-image fa-2x"></i><br>
                                        <small>No Image</small>
                                    </div>
                                <?php else: ?>
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image fa-2x"></i><br>
                                        <small>No Image</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-2">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($row['category_name']); ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No categories found. Please check your database connection.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Debug Information (remove in production) -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-secondary">
                    <h5>Debug Information:</h5>
                    <p><strong>Total Categories:</strong> <?php echo $result ? $result->num_rows : 0; ?></p>
                    <p><strong>Base URL:</strong> <?php echo $baseURL; ?></p>
                    <p><strong>Image Base Path:</strong> <?php echo $imageBasePath; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function navigateToCategory(categoryId) {
            // Show loading spinner
            document.querySelector('.loading-spinner').style.display = 'block';
            
            // Add slight delay to show loading state
            setTimeout(() => {
                window.location.href = `course_detail.php?category_id=${categoryId}`;
            }, 300);
        }
        
        // Image loading debug
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.card-img-top');
            console.log(`Found ${images.length} images to load`);
            
            images.forEach((img, index) => {
                img.addEventListener('load', function() {
                    console.log(`Image ${index + 1} loaded successfully:`, this.src);
                });
                
                img.addEventListener('error', function() {
                    console.log(`Image ${index + 1} failed to load:`, this.src);
                });
            });
        });
        
        // Add click feedback
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>
</html>

<?php
// Close the database connection
if (isset($conn)) {
    $conn->close();
}
?>