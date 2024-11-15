<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
   <style>
      /* Add your CSS styles here */
      /* Toggle Styles */
      .nav-pills>li>a {
         border-radius: 0;
      }

      #wrapper {
         padding-left: 0;
         transition: all 0.5s ease;
         overflow: hidden;
      }

      #wrapper.toggled {
         padding-left: 250px;
         overflow: hidden;
      }

      #sidebar-wrapper {
         z-index: 1000;
         position: absolute;
         left: 250px;
         width: 0;
         height: 100%;
         margin-left: -250px;
         overflow-y: auto;
         background: #000;
         transition: all 0.5s ease;
      }

      #wrapper.toggled #sidebar-wrapper {
         width: 250px;
      }

      #page-content-wrapper {
         position: absolute;
         padding: 15px;
         width: 100%;
         overflow-x: hidden;
      }

      .sidebar-nav {
         position: absolute;
         top: 0;
         width: 250px;
         margin: 0;
         padding: 0;
         list-style: none;
      }

      .sidebar-nav li {
         text-indent: 15px;
         line-height: 40px;
      }

      .sidebar-nav li a {
         display: block;
         text-decoration: none;
         color: #999999;
      }

      .sidebar-nav li a:hover {
         text-decoration: none;
         color: #fff;
         background:#000;
      }

      .no-margin {
         margin: 0;
      }
   </style>
</head>

<body>
   <nav class="navbar navbar-default no-margin">
      <div class="navbar-header fixed-brand">
         <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" id="menu-toggle">
            <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
         </button>
         <a class="navbar-brand" href="#"><i class="fa fa-rocket fa-4"></i> M-33</a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
         <ul class="nav navbar-nav">
            <li class="active"></li>
         </ul>
      </div>
   </nav>

   <div id="wrapper">
      <!-- Sidebar -->
      <div id="sidebar-wrapper">
         <div class="sidebar">
            <img src="<?php echo 'uploads/' . htmlspecialchars($user['photo']); ?>" alt="Profile Photo" style="border-radius: 50%; width: 100px; height: 100px;">
            <ul class="sidebar-nav nav-pills nav-stacked" id="menu">
               <li><a href="admin_dashboard.php">Dashboard</a></li>
               <li><a href="quiz_management.php">Quiz Management</a></li>
               <li><a href="quiz_page.php">Quiz Page</a></li>
               <li><a href="users_details.php">Users</a></li>
               <li><a href="questions.php">Questions</a></li>
               <li><a href="add_question.php">Add Questions</a></li>
               <li><a href="category_management.php">Categories</a></li>
               <li><a href="subcategory_management.php">Subcategories</a></li>
               <li><a href="add_services_courses.php">Add Services & Courses</a></li>
               <li><a href="view_results.php">View Results</a></li>
               <li><a href="approve_testimonial.php">approve_testimonial</a></li>
               <li><a href="approve_user_questions.php">Approve_user_questions</a></li>
               <li><a href="#">Reports</a></li>
               <li><a href="#">Settings</a></li>
               <li><a href="../logout.php">Logout</a></li>
            </ul>
         </div>
      </div>
      <!-- /#sidebar-wrapper -->

      

   <!-- jQuery -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

   <script>
      $("#menu-toggle").click(function (e) {
         e.preventDefault();
         $("#wrapper").toggleClass("toggled");
      });

      function initMenu() {
         $('#menu ul').hide();
         $('#menu li a').click(function () {
            var checkElement = $(this).next();
            if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
               return false;
            }
            if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
               $('#menu ul:visible').toggle('normal');
               checkElement.slideDown('normal');
               return false;
            }
         });
      }
      $(document).ready(function () {
         initMenu();
      });
   </script>
</body>
</html>
