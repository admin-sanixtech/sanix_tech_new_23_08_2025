<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technology</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo"><?php echo 'Sanix Technology'; ?></div>
            <div class="contact-info">
            <span><?php echo '+919392508648'; ?></span>
            <span><?php echo 'info@sanixtech.in'; ?></span>
            <a href="register.php" class="btn btn-primary">Register</a> <!-- Link to Register Page -->
            <a href="login.php" class="btn btn-primary">Login</a> <!-- Link to Login Page -->
            </div>

        </div>
    </header>
    <nav>
        <ul class="nav-menu">
            <li><a href="#">Services</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Projects</a></li>
            <li><a href="careers.php">Careers</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </nav>
    
    <?php include 'slider.php'; ?>
    <?php include 'cards.php'; ?>
    <?php include 'testimonials.php'; ?>
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script type="text/javascript">
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
