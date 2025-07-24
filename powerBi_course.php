<?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5">
    <h2 class="text-center mb-4">Power BI Course Content</h2>

    <div class="accordion" id="powerBiCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to Power BI
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Power BI?</li>
                        <li>Power BI Ecosystem and Architecture</li>
                        <li>Installing Power BI Desktop</li>
                        <li>Power BI Interface Overview</li>
                        <li>Importing Data from Different Sources</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Data Preparation & Modeling
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Data Cleaning and Transformation</li>
                        <li>Using Power Query Editor</li>
                        <li>Creating Relationships Between Tables</li>
                        <li>Star and Snowflake Schema</li>
                        <li>Data Types and Formatting</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Data Visualization
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Creating Reports and Dashboards</li>
                        <li>Using Built-in Visuals</li>
                        <li>Custom Visuals and Marketplace</li>
                        <li>Best Practices for Visualization</li>
                        <li>Formatting and Interactions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: DAX (Data Analysis Expressions)
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to DAX</li>
                        <li>Calculated Columns vs Measures</li>
                        <li>Common DAX Functions (SUM, AVERAGE, COUNT, etc.)</li>
                        <li>Time Intelligence Functions</li>
                        <li>Using Variables in DAX</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Filters, Slicers, and Drill-through
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Using Filters in Reports</li>
                        <li>Page and Report Level Filters</li>
                        <li>Slicers and Syncing</li>
                        <li>Drill-Down, Drill-Up, and Drill-Through</li>
                        <li>Bookmarks and Tooltips</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Publishing and Sharing
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Publishing to Power BI Service</li>
                        <li>Creating and Managing Workspaces</li>
                        <li>Sharing Reports and Dashboards</li>
                        <li>Setting Up Scheduled Refresh</li>
                        <li>Row-Level Security (RLS)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: Power BI with Excel & Other Tools
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Connecting Excel to Power BI</li>
                        <li>Using Analyze in Excel</li>
                        <li>Integrating with Power Apps and Power Automate</li>
                        <li>Exporting Data and Visuals</li>
                        <li>Embedding Reports</li>
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
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#powerBiCourse">
                <div class="accordion-body">
                    <ul>
                        <li>End-to-End Power BI Dashboard Project</li>
                        <li>Importing and Transforming Real-World Data</li>
                        <li>Building Advanced Visual Reports</li>
                        <li>Using DAX for Insightful Metrics</li>
                        <li>Publishing and Sharing Final Reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
