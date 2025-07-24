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
  <div class="center-accordion" > 
    <h2 class="text-center mb-4">Python Programming Course Content</h2>

    <div class="accordion" id="pythonCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="false" aria-controls="moduleOne">
                    Module 1: Introduction to Python
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Python?</li>
                        <li>Features of Python</li>
                        <li>Python installation and setup</li>
                        <li>Python IDEs (IDLE, VS Code, PyCharm)</li>
                        <li>Running Python scripts</li>
                        <li>First Python program</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Python Basics
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Variables and Data Types</li>
                        <li>Type Conversion</li>
                        <li>Operators in Python</li>
                        <li>Input and Output Functions</li>
                        <li>Comments and Code Structure</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Control Flow
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>if, elif, else statements</li>
                        <li>Nested conditions</li>
                        <li>Loops (for, while)</li>
                        <li>break, continue, pass</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Data Structures
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Lists, Tuples, Sets, Dictionaries</li>
                        <li>List Comprehension</li>
                        <li>Common Methods and Operations</li>
                        <li>Nested Data Structures</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Functions and Modules
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Defining and calling functions</li>
                        <li>Arguments and Return Values</li>
                        <li>Lambda Functions</li>
                        <li>Built-in functions</li>
                        <li>Importing and creating modules</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Object-Oriented Programming (OOP)
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Classes and Objects</li>
                        <li>Constructors and Destructors</li>
                        <li>Inheritance</li>
                        <li>Encapsulation and Polymorphism</li>
                        <li>Magic Methods</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: File Handling & Exception Handling
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Reading and Writing Files</li>
                        <li>File Modes and Context Managers</li>
                        <li>Error Types and Exceptions</li>
                        <li>try-except-else-finally Blocks</li>
                        <li>Custom Exceptions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: Advanced Python (Optional)
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#pythonCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Iterators and Generators</li>
                        <li>Decorators</li>
                        <li>Comprehensions</li>
                        <li>Multithreading</li>
                        <li>Working with JSON, CSV, XML</li>
                        <li>Intro to APIs</li>
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
