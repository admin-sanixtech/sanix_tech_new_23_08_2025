<?php
include 'db_connection.php';

// Fetch approved testimonials using PDO
$sql = "SELECT u.username, t.comment, t.created_at 
        FROM testimonials t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.approved = 1";
$stmt = $pdo->query($sql);
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        if (count($testimonials) > 0) {
            $isActive = true;

            foreach ($testimonials as $row) {
                echo '<div class="carousel-item' . ($isActive ? ' active' : '') . '">';
                echo '<div class="testimonial text-center">';
                echo '<p>"' . htmlspecialchars($row['comment']) . '"</p>';
                echo '<h5>- ' . htmlspecialchars($row['username']) . '</h5>';
                echo '</div>';
                echo '</div>';
                $isActive = false;
            }
        } else {
            echo '<div class="carousel-item active">';
            echo '<div class="testimonial text-center">';
            echo '<p>No approved testimonials available at the moment.</p>';
            echo '</div>';
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
