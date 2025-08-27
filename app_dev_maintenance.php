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
    <aside class="col-md-3 px-3"> 
    <h4 class="text-center mb-4">App Development & Maintenance Course Content</h4>

    <div class="accordion" id="appDevAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule1Heading">
          <button class="accordion-button" type="button" >
            Introduction to Mobile App Development
          </button>
        </h2>
        <div id="appModule1" class="accordion-collapse collapse show" aria-labelledby="appModule1Heading" data-bs-parent="#appDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Overview of Mobile Platforms (iOS, Android)</li>
              <li>Types of Mobile Apps: Native, Hybrid, Web</li>
              <li>App Development Lifecycle</li>
              <li>Popular Frameworks (Flutter, React Native)</li>
              <li>Tools & IDEs (Android Studio, Xcode)</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule2Heading">
          <button class="accordion-button" type="button" >
            UI/UX Design Principles
          </button>
        </h2>
        <div id="appModule2" class="accordion-collapse collapse show" aria-labelledby="appModule2Heading" data-bs-parent="#appDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Mobile UX Best Practices</li>
              <li>Wireframing and Prototyping Tools</li>
              <li>Material Design & Human Interface Guidelines</li>
              <li>Responsive Layouts & Components</li>
              <li>Accessibility Considerations</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule3Heading">
          <button class="accordion-button" type="button" >
            Backend Integration
          </button>
        </h2>
        <div id="appModule3" class="accordion-collapse collapse show" aria-labelledby="appModule3Heading" data-bs-parent="#appDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>RESTful APIs & JSON</li>
              <li>Authentication & Authorization</li>
              <li>Database Integration (Firebase, MySQL)</li>
              <li>Push Notifications</li>
              <li>Data Security Best Practices</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule4Heading">
          <button class="accordion-button" type="button">
             Testing & Deployment
          </button>
        </h2>
        <div id="appModule4" class="accordion-collapse collapse show" aria-labelledby="appModule4Heading" data-bs-parent="#appDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>App Testing Strategies</li>
              <li>Unit Testing & Debugging</li>
              <li>App Store & Play Store Guidelines</li>
              <li>Version Control & CI/CD Basics</li>
              <li>Publishing and Updates</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule5Heading">
          <button class="accordion-button" type="button">
             App Maintenance & Monitoring
          </button>
        </h2>
        <div id="appModule5" class="accordion-collapse collapse show" aria-labelledby="appModule5Heading" data-bs-parent="#appDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Bug Fixes & User Feedback Handling</li>
              <li>Performance Monitoring Tools</li>
              <li>Regular Security Patching</li>
              <li>Analytics & User Behavior Tracking</li>
              <li>Maintaining Backward Compatibility</li>
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
