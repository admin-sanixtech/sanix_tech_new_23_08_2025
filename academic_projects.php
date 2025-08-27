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
    <h2 class="text-center mb-4">Academic Project Services</h2>

    <div class="accordion" id="academicProjectAccordion">
 
      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="academicModule1Heading">
          <button class="accordion-button" type="button">
            Module 1: Project Ideation & Guidance
          </button>
        </h2>
        <div id="academicModule1" class="accordion-collapse collapse show" aria-labelledby="academicModule1Heading" data-bs-parent="#academicProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Topic Selection Support</li>
              <li>Feasibility Study & Guidance</li>
              <li>Domain-Specific Suggestions</li>
              <li>Abstract & Proposal Writing</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="academicModule2Heading">
          <button class="accordion-button" type="button" >
            Module 2: Technical Development Support
          </button>
        </h2>
        <div id="academicModule2" class="accordion-collapse collapse show" aria-labelledby="academicModule2Heading" data-bs-parent="#academicProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Software & Hardware Implementation</li>
              <li>Programming & Coding Assistance</li>
              <li>Project Documentation</li>
              <li>Tool & Technology Training</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="academicModule3Heading">
          <button class="accordion-button " type="button" >
            Module 3: Report & Presentation Preparation
          </button>
        </h2>
        <div id="academicModule3" class="accordion-collapse collapse show" aria-labelledby="academicModule3Heading" data-bs-parent="#academicProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>IEEE Format & Thesis Report Writing</li>
              <li>Plagiarism Check & Corrections</li>
              <li>PPT Creation & Review</li>
              <li>Project Viva Preparation</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="academicModule4Heading">
          <button class="accordion-button " type="button" >
            Module 4: Real-Time & Final-Year Projects
          </button>
        </h2>
        <div id="academicModule4" class="accordion-collapse collapse show" aria-labelledby="academicModule4Heading" data-bs-parent="#academicProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Live Project Integration</li>
              <li>Mini & Major Projects Support</li>
              <li>Client-Side Project Guidance</li>
              <li>Final Demo Assistance</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="academicModule5Heading">
          <button class="accordion-button" type="button">
            Module 5: Domains & Technologies
          </button>
        </h2>
        <div id="academicModule5" class="accordion-collapse collapse show" aria-labelledby="academicModule5Heading" data-bs-parent="#academicProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>AI/ML, IoT, Blockchain, Cybersecurity</li>
              <li>Cloud & Web App Projects</li>
              <li>Android & iOS Development</li>
              <li>Data Science & Analytics</li>
            </ul>
          </div>
        </div>
      </div>

    </div> <!-- /.accordion -->
</aside>
<div class="col-md-7" style="border-right: 2px solid #ccc">
  <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
    <h3 class="heading" >Select a topic to view details here</h3>
  </div>
  
</div>

  <?php include('right_sidebar.php'); ?>
   
  </div>
</div>


<?php include('footer.php'); ?>

</body>
</html>
