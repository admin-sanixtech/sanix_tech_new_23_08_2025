<?php
// Start session if needed
session_start();

// Include necessary files if any
// include 'db_connection.php'; // Uncomment if you need DB connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Sanix Technologies</title>
    <link rel="stylesheet" href="css/user_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <style>

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content h1 {
            color: blue;
            font-size: 36px;
            text-align: center;
        }
        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #333;
        }
        .content img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }
        .team-section {
            text-align: center;
        }
        .team-section h2 {
            font-size: 30px;
            margin-bottom: 20px;
        }
        .team-section .team-member {
            display: inline-block;
            margin: 15px;
            text-align: center;
        }
        .team-section .team-member img {
            border-radius: 50%;
            max-width: 150px;
            height: 150px;
        }
        .team-section .team-member h3 {
            font-size: 20px;
            margin-top: 10px;
        }
        .team-section .team-member p {
            font-size: 16px;
            color: #666;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>

    <div class="content">
        <h1>Who We Are</h1>
        <p>
            Welcome to Sanix Technology! We are a passionate team of professionals who strive to deliver 
            the best tech solutions for our clients. Our goal is to bring innovative ideas to life 
            through cutting-edge technology, providing reliable services, and building long-lasting 
            relationships with our clients.
        </p>
        <img src="path/to/company_image.jpg" alt="Sanix Technology Office"> <!-- Add your company image here -->

        <h1>Our Mission</h1>
        <p>
            At Sanix Technology, we aim to provide top-notch tech solutions that drive efficiency, 
            creativity, and success for our clients. We believe in the power of technology to transform 
            businesses, and our mission is to make that transformation smooth and successful for every 
            organization we work with.
        </p>

        <h1>Meet Our Team</h1>
        <div class="team-section">
            <h2>Our Experts</h2>
            <div class="team-member">
                <img src="images/sandeep_img2.jpeg" alt="Member 2">
                <h3> Mr Sandeepkumar Kasipeta</h3>
                <p>Lead Developer and Head of Operations</p>
            </div>
            <!-- Add more team members as needed -->
        </div>

        <h1>Our Values</h1>
        <p>
            We believe in quality, transparency, and innovation. Our core values guide us in every 
            project we undertake, ensuring that we deliver nothing but the best to our clients. We are 
            committed to creating solutions that not only meet the immediate needs of our clients but also 
            set them up for future success.
        </p>
    </div>

</body>
</html>
