<?php
include 'db_connection.php';

// Fetch approved testimonials
$sql = "SELECT u.name, t.comment, t.created_at 
        FROM testimonials t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.approved = 1";
$result = $conn->query($sql);
?>

<div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        if ($result->num_rows > 0) {
            $isActive = true; // Flag to set the first item as active

            while ($row = $result->fetch_assoc()) {
                // Open carousel item div and add "active" class only for the first item
                echo '<div class="carousel-item' . ($isActive ? ' active' : '') . '">';
                echo '<div class="testimonial text-center">'; // Center-align content for styling
                echo '<p>"' . htmlspecialchars($row['comment']) . '"</p>';
                echo '<h5>- ' . htmlspecialchars($row['name']) . '</h5>';
                echo '</div>';
                echo '</div>';
                
                // After the first item, remove the "active" class for subsequent items
                $isActive = false;
            }
        } else {
            // Display this message if no testimonials are available
            echo '<div class="carousel-item active">';
            echo '<p>No approved testimonials available at the moment.</p>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<?php
$conn->close();
?>
