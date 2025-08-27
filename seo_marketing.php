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
    <h4 class="text-center mb-4">SEO & Digital Marketing Course Content</h4>

    <div class="accordion" id="seoCourseAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="module1Heading">
          <button class="accordion-button" type="button" >
            Introduction to SEO
          </button>
        </h2>
        <div id="module1" class="accordion-collapse collapse show" aria-labelledby="module1Heading" data-bs-parent="#seoCourseAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is SEO?</li>
              <li>Types of SEO: On-page, Off-page, Technical</li>
              <li>Search Engine Algorithms</li>
              <li>How Search Engines Work</li>
              <li>SEO vs SEM</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="module2Heading">
          <button class="accordion-button" type="button">
             On-Page Optimization
          </button>
        </h2>
        <div id="module2" class="accordion-collapse collapse show" aria-labelledby="module2Heading" data-bs-parent="#seoCourseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Keyword Research Tools</li>
              <li>Meta Tags Optimization</li>
              <li>Content Strategy & Structure</li>
              <li>Internal Linking</li>
              <li>Image Optimization</li>
              <li>Mobile Responsiveness</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="module3Heading">
          <button class="accordion-button " type="button" >
             Off-Page SEO
          </button>
        </h2>
        <div id="module3" class="accordion-collapse collapse show" aria-labelledby="module3Heading" data-bs-parent="#seoCourseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Backlink Building Techniques</li>
              <li>Guest Blogging</li>
              <li>Forum & Blog Commenting</li>
              <li>Social Bookmarking</li>
              <li>Influencer Marketing</li>
              <li>Directory Submissions</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="module4Heading">
          <button class="accordion-button " type="button" >
            Digital Marketing Fundamentals
          </button>
        </h2>
        <div id="module4" class="accordion-collapse collapse show" aria-labelledby="module4Heading" data-bs-parent="#seoCourseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Overview of Digital Marketing</li>
              <li>Social Media Marketing (SMM)</li>
              <li>Content Marketing</li>
              <li>Email Marketing</li>
              <li>Google Ads & PPC</li>
              <li>Video Marketing</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="module5Heading">
          <button class="accordion-button " type="button" >
            SEO Tools & Analytics
          </button>
        </h2>
        <div id="module5" class="accordion-collapse collapse show" aria-labelledby="module5Heading" data-bs-parent="#seoCourseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Google Search Console</li>
              <li>Google Analytics</li>
              <li>SEMRush / Ahrefs Basics</li>
              <li>Keyword Rank Trackers</li>
              <li>Site Audit Tools</li>
              <li>Monthly Reporting Techniques</li>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
