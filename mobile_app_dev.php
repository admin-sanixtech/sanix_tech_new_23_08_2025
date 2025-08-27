<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mobile App Development</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Mobile App Development</h2>

    <div class="accordion" id="mobileAppAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#appModule1" aria-expanded="true" aria-controls="appModule1">
            Module 1: Introduction to Mobile App Development
          </button>
        </h2>
        <div id="appModule1" class="accordion-collapse collapse show" aria-labelledby="appModule1Heading" data-bs-parent="#mobileAppAccordion">
          <div class="accordion-body">
            <ul>
              <li>Overview of Mobile Platforms</li>
              <li>Native vs Hybrid vs Web Apps</li>
              <li>Development Tools & IDEs</li>
              <li>App Lifecycle</li>
              <li>Market Trends</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appModule2" aria-expanded="false" aria-controls="appModule2">
            Module 2: Android App Development
          </button>
        </h2>
        <div id="appModule2" class="accordion-collapse collapse" aria-labelledby="appModule2Heading" data-bs-parent="#mobileAppAccordion">
          <div class="accordion-body">
            <ul>
              <li>Java/Kotlin Basics</li>
              <li>Android Studio and Emulator</li>
              <li>Activities and Intents</li>
              <li>Layouts and UI Components</li>
              <li>Permissions and Security</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appModule3" aria-expanded="false" aria-controls="appModule3">
            Module 3: iOS App Development
          </button>
        </h2>
        <div id="appModule3" class="accordion-collapse collapse" aria-labelledby="appModule3Heading" data-bs-parent="#mobileAppAccordion">
          <div class="accordion-body">
            <ul>
              <li>Swift Programming Basics</li>
              <li>Xcode Setup</li>
              <li>ViewControllers and Navigation</li>
              <li>Storyboard and UI Design</li>
              <li>App Store Guidelines</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appModule4" aria-expanded="false" aria-controls="appModule4">
            Module 4: Cross-Platform Frameworks
          </button>
        </h2>
        <div id="appModule4" class="accordion-collapse collapse" aria-labelledby="appModule4Heading" data-bs-parent="#mobileAppAccordion">
          <div class="accordion-body">
            <ul>
              <li>Flutter Basics</li>
              <li>React Native Overview</li>
              <li>Code Sharing Concepts</li>
              <li>UI Consistency Across Platforms</li>
              <li>Third-party Libraries</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="appModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appModule5" aria-expanded="false" aria-controls="appModule5">
            Module 5: App Deployment & Maintenance
          </button>
        </h2>
        <div id="appModule5" class="accordion-collapse collapse" aria-labelledby="appModule5Heading" data-bs-parent="#mobileAppAccordion">
          <div class="accordion-body">
            <ul>
              <li>Play Store & App Store Publishing</li>
              <li>Debugging & Testing</li>
              <li>Version Control (Git)</li>
              <li>Crash Reporting & Monitoring</li>
              <li>Updates & User Feedback</li>
            </ul>
          </div>
        </div>
      </div>

    </div> <!-- /.accordion -->
  </div>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
