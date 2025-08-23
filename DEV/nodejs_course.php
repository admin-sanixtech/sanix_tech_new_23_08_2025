<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Node.js Course Content</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Node.js Course Content</h2>

    <div class="accordion" id="nodeCourse">

        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#nodeOne" aria-expanded="true" aria-controls="nodeOne">
                    Module 1: Introduction to Node.js
                </button>
            </h2>
            <div id="nodeOne" class="accordion-collapse collapse show" aria-labelledby="nodeHeadingOne" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Node.js?</li>
                        <li>Why Use Node.js?</li>
                        <li>Setting Up the Environment</li>
                        <li>Node.js Architecture</li>
                        <li>Running Your First Script</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeTwo" aria-expanded="false" aria-controls="nodeTwo">
                    Module 2: Modules and Packages
                </button>
            </h2>
            <div id="nodeTwo" class="accordion-collapse collapse" aria-labelledby="nodeHeadingTwo" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating Modules</li>
                        <li>Using Built-in Modules</li>
                        <li>npm and package.json</li>
                        <li>Installing Third-Party Packages</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeThree" aria-expanded="false" aria-controls="nodeThree">
                    Module 3: Asynchronous Programming
                </button>
            </h2>
            <div id="nodeThree" class="accordion-collapse collapse" aria-labelledby="nodeHeadingThree" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Callbacks</li>
                        <li>Promises</li>
                        <li>Async/Await</li>
                        <li>Error Handling</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeFour" aria-expanded="false" aria-controls="nodeFour">
                    Module 4: File System and Path
                </button>
            </h2>
            <div id="nodeFour" class="accordion-collapse collapse" aria-labelledby="nodeHeadingFour" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Reading and Writing Files</li>
                        <li>Working with Directories</li>
                        <li>Path Module</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeFive" aria-expanded="false" aria-controls="nodeFive">
                    Module 5: Creating Web Servers
                </button>
            </h2>
            <div id="nodeFive" class="accordion-collapse collapse" aria-labelledby="nodeHeadingFive" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>HTTP Module</li>
                        <li>Creating Basic Server</li>
                        <li>Handling Requests and Responses</li>
                        <li>Serving HTML and JSON</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeSix" aria-expanded="false" aria-controls="nodeSix">
                    Module 6: Express Framework
                </button>
            </h2>
            <div id="nodeSix" class="accordion-collapse collapse" aria-labelledby="nodeHeadingSix" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Installing Express</li>
                        <li>Creating Routes</li>
                        <li>Middleware and Request Handling</li>
                        <li>Error Handling</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeSeven" aria-expanded="false" aria-controls="nodeSeven">
                    Module 7: Working with Databases
                </button>
            </h2>
            <div id="nodeSeven" class="accordion-collapse collapse" aria-labelledby="nodeHeadingSeven" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to MongoDB</li>
                        <li>Connecting MongoDB with Mongoose</li>
                        <li>CRUD Operations</li>
                        <li>Schema and Models</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="nodeHeadingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nodeEight" aria-expanded="false" aria-controls="nodeEight">
                    Module 8: Authentication and Deployment
                </button>
            </h2>
            <div id="nodeEight" class="accordion-collapse collapse" aria-labelledby="nodeHeadingEight" data-bs-parent="#nodeCourse">
                <div class="accordion-body">
                    <ul>
                        <li>User Authentication with JWT</li>
                        <li>Sessions and Cookies</li>
                        <li>Deploying Node.js Apps</li>
                        <li>Environment Variables</li>
                        <li>Using Process Manager (PM2)</li>
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
