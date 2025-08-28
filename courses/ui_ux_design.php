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
    <aside class="col-md-3 px-3" > 
        <h4 class="text-center mb-4">UI/UX Course Content</h4>

        <div class="accordion" id="uiuxCourse">

            <!-- Module 1: Introduction to UI/UX -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="introHeading">
                    <button class="accordion-button" type="button">
                        Introduction to UI/UX Design
                    </button>
                </h2>
                <div id="introModule" class="accordion-collapse collapse show" aria-labelledby="introHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>What is UI and UX?</li>
                            <li>Difference Between UI & UX</li>
                            <li>Importance of User-Centered Design</li>
                            <li>Design Thinking Process</li>
                            <li>Roles of a UI/UX Designer</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 2: UX Research -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="researchHeading">
                    <button class="accordion-button " type="buttoon">
                     UX Research and Analysis
                    </button>
                </h2>
                <div id="researchModule" class="accordion-collapse collapse show" aria-labelledby="researchHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>User Research Methods</li>
                            <li>Creating Personas</li>
                            <li>User Journey Mapping</li>
                            <li>Competitive Analysis</li>
                            <li>Problem Definition and Opportunity Identification</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 3: UI Design Fundamentals -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="uiDesignHeading">
                    <button class="accordion-button" type="button" >
                         UI Design Fundamentals
                    </button>
                </h2>
                <div id="uiDesignModule" class="accordion-collapse collapse show" aria-labelledby="uiDesignHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>Color Theory and Typography</li>
                            <li>Layout Principles and Grids</li>
                            <li>Designing for Accessibility</li>
                            <li>Buttons, Forms, and Interactions</li>
                            <li>Consistency and Visual Hierarchy</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 4: Tools and Prototyping -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="toolsHeading">
                    <button class="accordion-button" type="button">
                         Prototyping Tools & Wireframing
                    </button>
                </h2>
                <div id="toolsModule" class="accordion-collapse collapse show" aria-labelledby="toolsHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>Introduction to Figma/Adobe XD</li>
                            <li>Low-fidelity vs High-fidelity Prototypes</li>
                            <li>Wireframing Best Practices</li>
                            <li>Interactive Prototyping</li>
                            <li>User Testing Techniques</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 5: Final Project -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="projectHeading">
                    <button class="accordion-button" type="button">
                        Capstone Project
                    </button>
                </h2>
                <div id="projectModule" class="accordion-collapse collapse show" aria-labelledby="projectHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>Design a Complete UI/UX Case Study</li>
                            <li>Conduct User Research</li>
                            <li>Build Wireframes and Prototypes</li>
                            <li>Conduct Usability Testing</li>
                            <li>Present Final Project with Rationale</li>
                        </ul>
                    </div>
                </div>
            </div>
            </aside>
        <div class="col-md-7" style="border-right: 2px solid #ccc">
         <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
           <h3 class="heading" >Select a topic to view details here</h3>
         </div>

            </div>

  <?php include('right_sidebar.php'); ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
