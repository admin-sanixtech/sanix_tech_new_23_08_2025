<?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5">
    <h2 class="text-center mb-4">SQL Course Content</h2>

    <div class="accordion" id="sqlCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to Databases & SQL
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is a Database?</li>
                        <li>Types of Databases</li>
                        <li>What is SQL?</li>
                        <li>SQL vs NoSQL</li>
                        <li>Installing MySQL / PostgreSQL / SQLite</li>
                        <li>SQL Syntax and Structure</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Data Definition Language (DDL)
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>CREATE, ALTER, DROP</li>
                        <li>Data Types in SQL</li>
                        <li>Constraints (PRIMARY KEY, FOREIGN KEY, UNIQUE, NOT NULL)</li>
                        <li>Default and Check Constraints</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Data Manipulation Language (DML)
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>INSERT, UPDATE, DELETE</li>
                        <li>Using WHERE Clause</li>
                        <li>Handling NULLs</li>
                        <li>Using RETURNING Clause (PostgreSQL)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Data Query Language (DQL)
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>SELECT Statement</li>
                        <li>Filtering with WHERE</li>
                        <li>ORDER BY, LIMIT, OFFSET</li>
                        <li>Aliases and Calculated Fields</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: SQL Functions and Aggregations
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>COUNT, SUM, AVG, MIN, MAX</li>
                        <li>GROUP BY and HAVING</li>
                        <li>String Functions</li>
                        <li>Date and Time Functions</li>
                        <li>Mathematical Functions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Joins and Subqueries
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>INNER JOIN, LEFT JOIN, RIGHT JOIN, FULL OUTER JOIN</li>
                        <li>Self Join</li>
                        <li>Cross Join</li>
                        <li>Subqueries (Single-row & Multi-row)</li>
                        <li>Correlated Subqueries</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: Views, Indexes and Transactions
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating and Managing Views</li>
                        <li>Indexes and Performance</li>
                        <li>Transactions and ACID Properties</li>
                        <li>COMMIT, ROLLBACK, SAVEPOINT</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: Advanced SQL Topics
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#sqlCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Window Functions (ROW_NUMBER, RANK, etc.)</li>
                        <li>Common Table Expressions (CTEs)</li>
                        <li>Recursive Queries</li>
                        <li>Triggers and Stored Procedures (Intro)</li>
                        <li>Working with JSON in SQL</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
