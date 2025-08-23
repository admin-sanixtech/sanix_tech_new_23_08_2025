<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ReactJS Course Content</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">ReactJS Course Content</h2>

    <div class="accordion" id="reactCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reactOne" aria-expanded="true" aria-controls="reactOne">
                    Module 1: Introduction to ReactJS
                </button>
            </h2>
            <div id="reactOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is React?</li>
                        <li>Why use React?</li>
                        <li>Setting up the Development Environment</li>
                        <li>Understanding JSX</li>
                        <li>Creating Your First React App</li>
                        <li>Folder Structure and Tools</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactTwo" aria-expanded="false" aria-controls="reactTwo">
                    Module 2: Components and Props
                </button>
            </h2>
            <div id="reactTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Function vs Class Components</li>
                        <li>Creating Reusable Components</li>
                        <li>Passing Data with Props</li>
                        <li>Component Composition</li>
                        <li>JSX Expressions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactThree" aria-expanded="false" aria-controls="reactThree">
                    Module 3: State and Events
                </button>
            </h2>
            <div id="reactThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>useState Hook</li>
                        <li>Handling Events</li>
                        <li>Conditional Rendering</li>
                        <li>Lists and Keys</li>
                        <li>Lifting State Up</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactFour" aria-expanded="false" aria-controls="reactFour">
                    Module 4: Hooks and Lifecycle
                </button>
            </h2>
            <div id="reactFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Understanding useEffect</li>
                        <li>Component Lifecycle in Functional Components</li>
                        <li>Rules of Hooks</li>
                        <li>Custom Hooks</li>
                        <li>Using Multiple Hooks</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactFive" aria-expanded="false" aria-controls="reactFive">
                    Module 5: Routing and Navigation
                </button>
            </h2>
            <div id="reactFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>React Router Basics</li>
                        <li>Dynamic Routing</li>
                        <li>Navigating Between Pages</li>
                        <li>Route Parameters</li>
                        <li>404 Pages and Redirects</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactSix" aria-expanded="false" aria-controls="reactSix">
                    Module 6: Forms and Controlled Components
                </button>
            </h2>
            <div id="reactSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Handling Form Inputs</li>
                        <li>Controlled vs Uncontrolled Components</li>
                        <li>Form Submission</li>
                        <li>Form Validation Basics</li>
                        <li>Working with Libraries (Formik, Yup)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactSeven" aria-expanded="false" aria-controls="reactSeven">
                    Module 7: HTTP and API Integration
                </button>
            </h2>
            <div id="reactSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Fetching Data with Fetch and Axios</li>
                        <li>Using useEffect for API Calls</li>
                        <li>Loading and Error States</li>
                        <li>Displaying API Data</li>
                        <li>Post and Put Requests</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reactEight" aria-expanded="false" aria-controls="reactEight">
                    Module 8: React Best Practices (Optional)
                </button>
            </h2>
            <div id="reactEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#reactCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Folder Structure</li>
                        <li>Component Reusability</li>
                        <li>Performance Optimization</li>
                        <li>Using Context API</li>
                        <li>Testing with Jest and React Testing Library</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
