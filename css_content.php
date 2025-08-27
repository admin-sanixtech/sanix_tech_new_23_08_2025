<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php

function css_content_fun() {
    // Add comments for clarity, describing the purpose of each link

    // Load fonts from Google Fonts
    echo '<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i,900,900i" rel="stylesheet">';
    echo '<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">';
    echo '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">';

    // Set website title
    echo '<title>Sanix Technology</title>';

    // Add favicon
    echo '<link rel="shortcut icon" type="image/icon" href="assets/images/logo/favicon.png">';

    // Load CSS libraries and custom styles
    echo '<link rel="stylesheet" href="css/font-awesome.min.css">';  // Font Awesome icons
    echo '<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">';  // Linear Icons
    echo '<link rel="stylesheet" href="css/animate.css">';  // Animate.css animations
    echo '<link rel="stylesheet" href="css/hover-min.css">';  // Hover.css effects
    echo '<link rel="stylesheet" href="css/magnific-popup.css">';  // Magnific Popup library
    echo '<link rel="stylesheet" href="css/owl.carousel.min.css">';  // Owl Carousel
    echo '<link href="assets/css/owl.theme.default.min.css" rel="stylesheet">';  // Owl Carousel theme
    echo '<link rel="stylesheet" href="css/bootstrap.min.css">';  // Bootstrap
    echo '<link href="css/bootsnav.css" rel="stylesheet">';  // Bootsnav navigation
    echo '<link rel="stylesheet" href="css/style.css">';  // Your main style sheet
    echo '<link rel="stylesheet" href="css/menu_dropdown.css">';  // Additional menu styles
    echo '<link rel="stylesheet" href="css/responsive.css">';  // Responsive styles

    // Remove commented-out code for IE8 support (unless necessary)
    // // // // -->
}

?>
