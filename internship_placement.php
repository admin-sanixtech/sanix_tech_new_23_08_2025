<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Internship & Placement Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Internship & Placement Services</h2>

    <div class="accordion" id="internshipAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="internshipModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#internshipModule1" aria-expanded="true" aria-controls="internshipModule1">
            Module 1: Introduction to Internship & Placement
          </button>
        </h2>
        <div id="internshipModule1" class="accordion-collapse collapse show" aria-labelledby="internshipModule1Heading" data-bs-parent="#internshipAccordion">
          <div class="accordion-body">
            <ul>
              <li>Importance of Internships & Placements</li>
              <li>Career Planning Essentials</li>
              <li>Overview of Campus Placement Process</li>
              <li>Industry Expectations from Fresh Graduates</li>
              <li>Building a Career Roadmap</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="internshipModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#internshipModule2" aria-expanded="false" aria-controls="internshipModule2">
            Module 2: Resume Building & Online Presence
          </button>
        </h2>
        <div id="internshipModule2" class="accordion-collapse collapse" aria-labelledby="internshipModule2Heading" data-bs-parent="#internshipAccordion">
          <div class="accordion-body">
            <ul>
              <li>Effective Resume & Cover Letter Writing</li>
              <li>LinkedIn Profile Optimization</li>
              <li>Portfolio Creation (Tech & Non-Tech)</li>
              <li>Professional Branding & Visibility</li>
              <li>Do's & Don'ts in Job Applications</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="internshipModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#internshipModule3" aria-expanded="false" aria-controls="internshipModule3">
            Module 3: Interview Preparation
          </button>
        </h2>
        <div id="internshipModule3" class="accordion-collapse collapse" aria-labelledby="internshipModule3Heading" data-bs-parent="#internshipAccordion">
          <div class="accordion-body">
            <ul>
              <li>Types of Interviews (HR, Technical, Panel)</li>
              <li>Mock Interviews & Feedback</li>
              <li>Commonly Asked Questions</li>
              <li>Communication & Soft Skills</li>
              <li>Grooming & Etiquette</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="internshipModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#internshipModule4" aria-expanded="false" aria-controls="internshipModule4">
            Module 4: Internship & Placement Support
          </button>
        </h2>
        <div id="internshipModule4" class="accordion-collapse collapse" aria-labelledby="internshipModule4Heading" data-bs-parent="#internshipAccordion">
          <div class="accordion-body">
            <ul>
              <li>Internship Matching Process</li>
              <li>Company Collaboration & Tie-ups</li>
              <li>Placement Drives & Events</li>
              <li>Tracking Progress & Feedback</li>
              <li>Certificate & Completion Support</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="internshipModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#internshipModule5" aria-expanded="false" aria-controls="internshipModule5">
            Module 5: Career Advancement & Lifelong Learning
          </button>
        </h2>
        <div id="internshipModule5" class="accordion-collapse collapse" aria-labelledby="internshipModule5Heading" data-bs-parent="#internshipAccordion">
          <div class="accordion-body">
            <ul>
              <li>Upskilling & Certification Guidance</li>
              <li>Career Counseling & Mentorship</li>
              <li>Alumni Network Support</li>
              <li>Job Switch & Career Growth Strategies</li>
              <li>Continuous Learning Culture</li>
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
