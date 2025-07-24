<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'navbar.php'; ?> <!-- includew your common navbar -->
    <?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5 d-flex justify-content-center">
    <div class="center-accordion">
    <h2 class="text-center mb-4">Web Development Course Content</h2>

    <div class="accordion" id="webDevCourse">

        <!-- Module 1: HTML -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="htmlHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#htmlModule" aria-expanded="true" aria-controls="htmlModule">
                    Module 1: HTML (HyperText Markup Language)
                </button>
            </h2>
            <div id="htmlModule" class="accordion-collapse collapse show" aria-labelledby="htmlHeading" data-bs-parent="#webDevCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is HTML?</li>
                        <li>HTML Document Structure</li>
                        <li>Headings, Paragraphs, Lists</li>
                        <li>Links, Images, Tables</li>
                        <li>Forms and Input Types</li>
                        <li>Semantic Tags (header, footer, section, article)</li>
                        <li>Audio & Video embedding</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2: CSS -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="cssHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cssModule" aria-expanded="false" aria-controls="cssModule">
                    Module 2: CSS (Cascading Style Sheets)
                </button>
            </h2>
            <div id="cssModule" class="accordion-collapse collapse" aria-labelledby="cssHeading" data-bs-parent="#webDevCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to CSS</li>
                        <li>Selectors and Properties</li>
                        <li>Box Model (margin, border, padding)</li>
                        <li>Display: block, inline, flex, grid</li>
                        <li>Positioning: static, relative, absolute, fixed, sticky</li>
                        <li>Responsive Design with Media Queries</li>
                        <li>CSS Flexbox and Grid</li>
                        <li>Animations and Transitions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3: JavaScript -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="jsHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#jsModule" aria-expanded="false" aria-controls="jsModule">
                    Module 3: JavaScript (JS)
                </button>
            </h2>
            <div id="jsModule" class="accordion-collapse collapse" aria-labelledby="jsHeading" data-bs-parent="#webDevCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to JavaScript</li>
                        <li>Variables, Data Types, Operators</li>
                        <li>Functions and Scope</li>
                        <li>Conditionals and Loops</li>
                        <li>DOM Manipulation (getElementById, querySelector, etc.)</li>
                        <li>Events: click, submit, keypress, etc.</li>
                        <li>Form Validation</li>
                        <li>JavaScript ES6 Features (let, const, arrow functions, etc.)</li>
                        <li>Introduction to JSON & APIs</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4: Final Project -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="projectHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectModule" aria-expanded="false" aria-controls="projectModule">
                    Module 4: Capstone Project
                </button>
            </h2>
            <div id="projectModule" class="accordion-collapse collapse" aria-labelledby="projectHeading" data-bs-parent="#webDevCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Build a Complete Responsive Website</li>
                        <li>Use HTML, CSS, and JavaScript together</li>
                        <li>Form submission and validation</li>
                        <li>Deploy project on GitHub/Netlify</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
 </div>
</div>

<?php include('footer.php'); ?> <!-- Include footer -->
</body>
</html>
