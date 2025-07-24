<?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5">
    <h2 class="text-center mb-4">C Programming Course Content</h2>

    <div class="accordion" id="cCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to C Programming
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>History and Features of C</li>
                        <li>Structure of a C Program</li>
                        <li>Compiling and Executing a Program</li>
                        <li>Basic Input and Output</li>
                        <li>Data Types and Variables</li>
                        <li>Constants and Operators</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Control Flow
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Conditional Statements (if, else, switch)</li>
                        <li>Loops (while, do-while, for)</li>
                        <li>break and continue</li>
                        <li>goto Statement</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Functions in C
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Function Declaration and Definition</li>
                        <li>Function Calling</li>
                        <li>Passing Arguments (Call by Value & Reference)</li>
                        <li>Recursion</li>
                        <li>Storage Classes</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Arrays and Strings
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>One-Dimensional Arrays</li>
                        <li>Two-Dimensional Arrays</li>
                        <li>Strings and String Handling Functions</li>
                        <li>Array of Strings</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Pointers in C
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to Pointers</li>
                        <li>Pointer Arithmetic</li>
                        <li>Pointers and Functions</li>
                        <li>Pointers and Arrays</li>
                        <li>Dynamic Memory Allocation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Structures and Unions
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Defining Structures</li>
                        <li>Arrays of Structures</li>
                        <li>Nested Structures</li>
                        <li>Pointers to Structures</li>
                        <li>Unions and Their Usage</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: File Handling
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>File Types and Operations</li>
                        <li>Opening and Closing Files</li>
                        <li>Reading and Writing to Files</li>
                        <li>File Pointers and Modes</li>
                        <li>Command Line Arguments</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: C Projects and Practice
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#cCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Mini Projects Using C</li>
                        <li>Building CLI Applications</li>
                        <li>Debugging and Error Handling</li>
                        <li>Interview Practice Questions</li>
                        <li>Practical Assignments</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
