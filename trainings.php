<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Training Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Training Services</h2>

    <div class="accordion" id="trainingAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule1" aria-expanded="true" aria-controls="trainingModule1">
            Module 1: Orientation & Skill Assessment
          </button>
        </h2>
        <div id="trainingModule1" class="accordion-collapse collapse show" aria-labelledby="trainingModule1Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Initial Skill Gap Analysis</li>
              <li>Training Needs Assessment</li>
              <li>Orientation & Career Goal Setting</li>
              <li>Learning Path Customization</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule2" aria-expanded="false" aria-controls="trainingModule2">
            Module 2: Technical & Domain Training
          </button>
        </h2>
        <div id="trainingModule2" class="accordion-collapse collapse" aria-labelledby="trainingModule2Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Core Programming Languages</li>
              <li>Web & App Development</li>
              <li>Cloud, DevOps, and Database Technologies</li>
              <li>Industry-Specific Tools & Platforms</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule3" aria-expanded="false" aria-controls="trainingModule3">
            Module 3: Soft Skills & Communication
          </button>
        </h2>
        <div id="trainingModule3" class="accordion-collapse collapse" aria-labelledby="trainingModule3Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Presentation & Communication Skills</li>
              <li>Teamwork & Collaboration</li>
              <li>Leadership Essentials</li>
              <li>Business Etiquette</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule4" aria-expanded="false" aria-controls="trainingModule4">
            Module 4: Real-time Projects & Assessments
          </button>
        </h2>
        <div id="trainingModule4" class="accordion-collapse collapse" aria-labelledby="trainingModule4Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Project-based Learning</li>
              <li>Case Studies & Simulations</li>
              <li>Peer Review & Evaluations</li>
              <li>Hands-on Capstone Projects</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule5" aria-expanded="false" aria-controls="trainingModule5">
            Module 5: Certification & Career Support
          </button>
        </h2>
        <div id="trainingModule5" class="accordion-collapse collapse" aria-labelledby="trainingModule5Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Course Completion Certificate</li>
              <li>Mock Tests & Certification Prep</li>
              <li>Mentorship & Career Guidance</li>
              <li>Transition to Internship or Placement Tracks</li>
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
