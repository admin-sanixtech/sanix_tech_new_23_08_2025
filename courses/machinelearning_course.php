<?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5">
    <h2 class="text-center mb-4">Machine Learning Course Content</h2>

    <div class="accordion" id="mlCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to Machine Learning
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Machine Learning?</li>
                        <li>Types of Machine Learning (Supervised, Unsupervised, Reinforcement)</li>
                        <li>Applications of Machine Learning</li>
                        <li>ML vs AI vs Deep Learning</li>
                        <li>Installing Python and Jupyter Notebook</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Data Preprocessing
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Loading Data with Pandas</li>
                        <li>Handling Missing Data</li>
                        <li>Encoding Categorical Variables</li>
                        <li>Feature Scaling and Normalization</li>
                        <li>Splitting Dataset into Train/Test</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Supervised Learning Algorithms
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Linear Regression</li>
                        <li>Logistic Regression</li>
                        <li>K-Nearest Neighbors (KNN)</li>
                        <li>Support Vector Machines (SVM)</li>
                        <li>Decision Trees & Random Forest</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Unsupervised Learning Algorithms
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>K-Means Clustering</li>
                        <li>Hierarchical Clustering</li>
                        <li>Principal Component Analysis (PCA)</li>
                        <li>t-SNE Visualization</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Model Evaluation and Selection
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Confusion Matrix</li>
                        <li>Accuracy, Precision, Recall, F1-Score</li>
                        <li>Cross Validation</li>
                        <li>ROC Curve and AUC</li>
                        <li>Bias-Variance Trade-off</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Feature Engineering & Selection
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating New Features</li>
                        <li>Feature Importance</li>
                        <li>Recursive Feature Elimination (RFE)</li>
                        <li>Dimensionality Reduction Techniques</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: Introduction to Deep Learning
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Neural Networks Basics</li>
                        <li>Activation Functions</li>
                        <li>Using TensorFlow/Keras</li>
                        <li>Building a Basic Neural Network</li>
                        <li>Overfitting and Dropout</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: Final Project & Deployment
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#mlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>End-to-End ML Project</li>
                        <li>Data Collection to Deployment</li>
                        <li>Model Saving and Loading</li>
                        <li>Using Flask for Web Deployment</li>
                        <li>Introduction to Cloud Deployment (Heroku, AWS)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
