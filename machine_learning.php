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
    <h2 class="text-center mb-4">Machine Learning</h2>

    <div class="accordion" id="mlOverview">
        <!-- Section 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="overviewOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    What is Machine Learning?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="overviewOne" data-bs-parent="#mlOverview">
                <div class="accordion-body">
                    <ul>
                        <li>Machine Learning is a subset of Artificial Intelligence (AI)</li>
                        <li>Enables systems to learn from data and improve without explicit programming</li>
                        <li>Used in fraud detection, recommendation systems, medical diagnosis, and more</li>
                        <li>Three types: Supervised, Unsupervised, and Reinforcement Learning</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="overviewTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Tools & Technologies
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="overviewTwo" data-bs-parent="#mlOverview">
                <div class="accordion-body">
                    <ul>
                        <li>Programming Language: Python</li>
                        <li>Libraries: Scikit-learn, Pandas, NumPy, Matplotlib, TensorFlow, Keras</li>
                        <li>Environments: Jupyter Notebook, Google Colab, Anaconda</li>
                        <li>Deployment: Flask, FastAPI, Heroku, AWS</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="overviewThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Key Learning Outcomes
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="overviewThree" data-bs-parent="#mlOverview">
                <div class="accordion-body">
                    <ul>
                        <li>Understand core ML concepts and algorithms</li>
                        <li>Clean, preprocess, and explore data using Python</li>
                        <li>Build and evaluate ML models for real-world datasets</li>
                        <li>Work on hands-on projects from scratch to deployment</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="overviewFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Career Paths & Applications
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="overviewFour" data-bs-parent="#mlOverview">
                <div class="accordion-body">
                    <ul>
                        <li>Data Scientist</li>
                        <li>Machine Learning Engineer</li>
                        <li>AI Researcher</li>
                        <li>Business Intelligence Developer</li>
                        <li>Applications in healthcare, finance, marketing, e-commerce, and more</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="overviewFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    Prerequisites
                </button>
            </h2>
            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="overviewFive" data-bs-parent="#mlOverview">
                <div class="accordion-body">
                    <ul>
                        <li>Basic knowledge of Python programming</li>
                        <li>Understanding of statistics and linear algebra</li>
                        <li>Familiarity with data structures and algorithms is a plus</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
 </div> 
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
</body>
</html>

