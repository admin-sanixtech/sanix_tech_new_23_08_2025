<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Angular Course Content</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Angular Course Content</h2>

    <div class="accordion" id="angularCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#angularOne" aria-expanded="true" aria-controls="angularOne">
                    Module 1: Introduction to Angular
                </button>
            </h2>
            <div id="angularOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Angular?</li>
                        <li>Angular vs AngularJS</li>
                        <li>Angular Architecture Overview</li>
                        <li>Setting up Angular Environment</li>
                        <li>Creating First Angular App</li>
                        <li>Understanding Angular CLI</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularTwo" aria-expanded="false" aria-controls="angularTwo">
                    Module 2: TypeScript Fundamentals
                </button>
            </h2>
            <div id="angularTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to TypeScript</li>
                        <li>Types and Interfaces</li>
                        <li>Classes and Objects</li>
                        <li>Modules and Decorators</li>
                        <li>Arrow Functions and Generics</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularThree" aria-expanded="false" aria-controls="angularThree">
                    Module 3: Components and Data Binding
                </button>
            </h2>
            <div id="angularThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating Components</li>
                        <li>Component Lifecycle</li>
                        <li>Templates and Styles</li>
                        <li>String Interpolation</li>
                        <li>Property, Event & Two-way Binding</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularFour" aria-expanded="false" aria-controls="angularFour">
                    Module 4: Directives and Pipes
                </button>
            </h2>
            <div id="angularFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Built-in Structural Directives</li>
                        <li>Attribute Directives</li>
                        <li>Custom Directives</li>
                        <li>Angular Pipes</li>
                        <li>Creating Custom Pipes</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularFive" aria-expanded="false" aria-controls="angularFive">
                    Module 5: Services and Dependency Injection
                </button>
            </h2>
            <div id="angularFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating and Injecting Services</li>
                        <li>Understanding Injectable</li>
                        <li>Hierarchical Injection</li>
                        <li>Using HTTPClient</li>
                        <li>Observables and RxJS</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularSix" aria-expanded="false" aria-controls="angularSix">
                    Module 6: Routing and Navigation
                </button>
            </h2>
            <div id="angularSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>RouterModule Setup</li>
                        <li>Defining Routes</li>
                        <li>Navigation with RouterLink</li>
                        <li>Route Guards</li>
                        <li>Lazy Loading Modules</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularSeven" aria-expanded="false" aria-controls="angularSeven">
                    Module 7: Forms in Angular
                </button>
            </h2>
            <div id="angularSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Template-Driven Forms</li>
                        <li>Reactive Forms</li>
                        <li>Form Validation</li>
                        <li>Dynamic Forms</li>
                        <li>FormBuilder and FormGroup</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#angularEight" aria-expanded="false" aria-controls="angularEight">
                    Module 8: Angular Best Practices (Optional)
                </button>
            </h2>
            <div id="angularEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#angularCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Project Structure</li>
                        <li>Reusable Components</li>
                        <li>Lazy Loading & Performance Optimization</li>
                        <li>State Management Overview (NgRx)</li>
                        <li>Testing with Jasmine and Karma</li>
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
