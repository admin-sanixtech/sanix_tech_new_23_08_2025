<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include your database connection file
include 'db_connection.php';

// Query to get the total counts
$total_projects = 0;
$total_users = 0;
$total_questions = 0;

$sql_projects = "SELECT COUNT(*) as total_projects FROM projects"; // Modify table name if needed
$sql_users = "SELECT COUNT(*) as total_users FROM users"; // Modify table name if needed
$sql_questions = "SELECT COUNT(*) as total_questions FROM quiz_questions"; // Modify table name if needed

$result_projects = $conn->query($sql_projects);
$result_users = $conn->query($sql_users);
$result_questions = $conn->query($sql_questions);

if ($result_projects->num_rows > 0) {
    $row = $result_projects->fetch_assoc();
    $total_projects = $row['total_projects'];
}

if ($result_users->num_rows > 0) {
    $row = $result_users->fetch_assoc();
    $total_users = $row['total_users'];
}

if ($result_questions->num_rows > 0) {
    $row = $result_questions->fetch_assoc();
    $total_questions = $row['total_questions'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-zR2sul2oRHkLHv7zFQHmD+kUblLSQK10/xJ7gQTG7yXJsxY8oU1tK9iYz94gkUCQ1hG7vKG47n7/y6oFHP+1ey9gChQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
        $('.slider').slick({
            autoplay: true,
            autoplaySpeed: 2000, // Adjust to your preferred speed
            dots: true,
            fade: true,
            speed: 1000,
            cssEase: 'linear',
            arrows: false // Remove navigation arrows if desired
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
