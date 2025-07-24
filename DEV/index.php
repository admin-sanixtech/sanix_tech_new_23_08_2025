<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your database connection file
include 'db_connection.php';

// Initialize totals
$total_projects = 0;
$total_users = 0;
$total_questions = 0;

try {
    // Prepare and execute queries
    $stmt1 = $pdo->query("SELECT COUNT(*) AS total_projects FROM projects");
    $stmt2 = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
    $stmt3 = $pdo->query("SELECT COUNT(*) AS total_questions FROM quiz_questions");

    // Fetch values
    $total_projects = $stmt1->fetch(PDO::FETCH_ASSOC)['total_projects'] ?? 0;
    $total_users    = $stmt2->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;
    $total_questions = $stmt3->fetch(PDO::FETCH_ASSOC)['total_questions'] ?? 0;
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>

    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>
    <?php include 'slider.php'; ?>
    <?php include 'cards_one.php'; ?>
    <?php include 'stats.php'; ?>
    <?php include 'cards_two.php'; ?>
    <?php include 'testimonials.php'; ?>
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $('.slider').slick({
            autoplay: true,
            autoplaySpeed: 2000,
            dots: true,
            fade: true,
            speed: 1000,
            cssEase: 'linear',
            arrows: false
        });
        $(document).ready(function(){
            $('.slider').slick({
                autoplay: true,
                autoplaySpeed: 2000,
                dots: true,
                fade: true,
                speed: 1000,
                appendDots: $('.slider')
            });
        });
    </script>

</body>
</html>
