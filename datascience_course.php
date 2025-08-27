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
       <?php include('header.php'); ?> <!-- Include your common header -->

      <?php include 'navbar.php'; ?> <!-- includew your common navbar -->

  <div class="container-fluid my-5 px-0">
  <div class="row gx-0">
     <!-- Sidebar Accordion -->
    <aside class="col-md-3 px-3"> 
    <h2 class="text-center mb-4">Data Science Course Content</h2>

    <div class="accordion" id="dataScienceCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to Data Science
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Data Science?</li>
                        <li>Data Science Lifecycle</li>
                        <li>Applications of Data Science</li>
                        <li>Tools Used in Data Science</li>
                        <li>Role of a Data Scientist</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Python for Data Science
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Python Basics Recap</li>
                        <li>Numpy for Numerical Computing</li>
                        <li>Pandas for Data Manipulation</li>
                        <li>Matplotlib & Seaborn for Visualization</li>
                        <li>Working with Jupyter Notebooks</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Data Wrangling and Preprocessing
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Handling Missing Data</li>
                        <li>Data Transformation & Normalization</li>
                        <li>Feature Engineering</li>
                        <li>Encoding Categorical Variables</li>
                        <li>Outlier Detection and Treatment</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Exploratory Data Analysis (EDA)
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Descriptive Statistics</li>
                        <li>Data Visualization Techniques</li>
                        <li>Univariate, Bivariate & Multivariate Analysis</li>
                        <li>Correlation and Heatmaps</li>
                        <li>Using Pandas Profiling</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Machine Learning Basics
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to Machine Learning</li>
                        <li>Supervised vs Unsupervised Learning</li>
                        <li>Train/Test Split and Cross Validation</li>
                        <li>Linear and Logistic Regression</li>
                        <li>Model Evaluation Metrics</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Classification & Clustering
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Decision Trees and Random Forests</li>
                        <li>K-Nearest Neighbors (KNN)</li>
                        <li>Naive Bayes Classifier</li>
                        <li>K-Means Clustering</li>
                        <li>Hierarchical Clustering</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: Model Deployment & Tools
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Model Saving with Pickle and Joblib</li>
                        <li>Intro to Flask for Deployment</li>
                        <li>Deploying ML Models on Web</li>
                        <li>Using Streamlit for Dashboards</li>
                        <li>Working with Google Colab</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: Capstone Project
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#dataScienceCourse">
                <div class="accordion-body">
                    <ul>
                        <li>End-to-End Data Science Project</li>
                        <li>Problem Definition and Dataset Selection</li>
                        <li>Data Cleaning, EDA and Modeling</li>
                        <li>Model Evaluation and Optimization</li>
                        <li>Deployment and Presentation</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</aside>
  </div>
</div>
<script>
 // Handle list item click
  document.querySelectorAll('.accordion-body li').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', function () {
      const text = this.textContent.trim();
      const content = contentMap[text] || `<h4>${text}</h4><p>Details coming soon...</p>`;
      document.getElementById('contentDisplay').innerHTML = content;

      // Optional: Visually highlight the active item
      document.querySelectorAll('.accordion-body li').forEach(li => li.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

<?php include('footer.php'); ?> <!-- Include your common footer -->
</body>
</html>
