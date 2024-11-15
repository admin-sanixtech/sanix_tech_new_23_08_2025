<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Content For Sidebar -->
<div class="h-100"><div class="sidebar-logo"><a href="#">Sanix Technology</a></div>
  <ul class="sidebar-nav">
    <li class="sidebar-header">User Elements</li>
    <li class="sidebar-item"><a href="/User/user_dashboard.php" class="sidebar-link"><i class="fa-solid fa-list pe-2"></i>Dashboard</a></li>
    <li class="sidebar-item"><a  href="#" class="sidebar-link collapsed"  data-bs-target="#Courses" data-bs-toggle="collapse"  aria-expanded="false" >
        <i class="fa-solid fa-file-lines pe-2"></i> Courses</a>
        <ul  id="Courses" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar" >
          <li class="sidebar-item"> <a href="/User/python.php" class="sidebar-link">Python</a></li>
          <li class="sidebar-item"> <a href="/User/sql.php" class="sidebar-link">SQL</a> </li>
          <li class="sidebar-item"> <a href="/User/power_bi.php" class="sidebar-link">Power BI</a> </li>
        </ul>
    </li>
    <li class="sidebar-item"><a  href="#"  class="sidebar-link collapsed"  data-bs-target="#Quiz" data-bs-toggle="collapse" aria-expanded="false">
      <i class="fa-solid fa-sliders pe-2"></i>TakeQuiz</a>
        <ul  id="Quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar" >
          <li class="sidebar-item"><a href="/User/python_quiz.php" class="sidebar-link">python</a></li>
          <li class="sidebar-item"><a href="/User/sql_quiz.php" class="sidebar-link">SQL</a></li>
          <li class="sidebar-item"><a href="/User/Digital Marketing_quiz.php" class="sidebar-link">Digital Marketing</a></li>
          <li class="sidebar-item"> <a href="/User/Powerbi_quiz.php" class="sidebar-link">Power BI</a></li>
          <li class="sidebar-item"> <a href="/User/azure_services_quiz.php" class="sidebar-link">Azure Services</a></li>
          <li class="sidebar-item"> <a href="/User/cyber_security_quiz.php" class="sidebar-link">Cyber Security</a></li>
          <li class="sidebar-item"> <a href="/User/AI_quiz.php" class="sidebar-link">AI</a></li>
          <li class="sidebar-item"> <a href="/User/datascience_quiz.php" class="sidebar-link">Data Science</a></li>
          <li class="sidebar-item"> <a href="/User/ml_quiz.php" class="sidebar-link">Machine Learning</a></li>
        </ul>
    </li>    
    <li class="sidebar-item"><a   href="#"  class="sidebar-link collapsed"  data-bs-target="#Material"  data-bs-toggle="collapse" aria-expanded="false"  >
          <i class="fa-regular fa-user pe-2"></i> Material  </a>
        <ul  id="Material"  class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar"  >
          <li class="sidebar-item"> <a href="/User/user_books.php" class="sidebar-link">Books</a> </li>
          <li class="sidebar-item"> <a href="/User/user_hand_written.php" class="sidebar-link">Hand Written Notes</a> </li>
          <li class="sidebar-item"> <a href="/User/user_cheet_sheets.php" class="sidebar-link">Cheet Sheets</a> </li>
          <li class="sidebar-item"> <a href="/User/user_vidoes.php" class="sidebar-link">Videos</a> </li>
        </ul>
    </li>
    <li class="sidebar-item">
        <a   href="#"  class="sidebar-link collapsed"  data-bs-target="#Upload"  data-bs-toggle="collapse" aria-expanded="false"  >
          <i class="fa-regular fa-user pe-2"></i>Upload</a>
        <ul  id="Upload"  class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar"  >
        <li class="sidebar-item"> <a href="/User/user_add_question.php" class="sidebar-link">Add Questions</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_add_post.php" class="sidebar-link">Add Post</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_add_testimonial.php" class="sidebar-link">Add Testimonial</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_interview_exp.php" class="sidebar-link">Share Interview Experience</a> </li>
        <li class="sidebar-item"> <a href="/User/user_books.php" class="sidebar-link">Books</a> </li>
        <li class="sidebar-item"> <a href="/User/user_hand_written.php" class="sidebar-link">Hand Written</a>  </li>
          
          
        </ul>
    </li>
    <li class="sidebar-item">
        <a   href="#"  class="sidebar-link collapsed"  data-bs-target="#Interview_Questions"  data-bs-toggle="collapse" aria-expanded="false"  >
          <i class="fa-regular fa-user pe-2"></i>Interview Questions</a>
        <ul  id="Interview_Questions"  class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar"  >
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">Python</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">SQL</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">Power BI</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">Azure Services</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">AWS</a>  </li>
        <li class="sidebar-item"> <a href="/User/user_view_python_int_question.php" class="sidebar-link">GCP</a>  </li>
          
        </ul>
    </li>

    <li class="sidebar-item"><a href="#" class="sidebar-link">Projects</a> </li>
    <li class="sidebar-item"><a href="/User/subscription_plans.php" class="sidebar-link">Subscription</a> </li>
    <li class="sidebar-item"><a href="/User/user_disscussions.php" class="sidebar-link">Disscussions</a> </li>
    <li class="sidebar-item"><a href="/User/withdrawal.php" class="sidebar-link">Withdrawal</a> </li>
    
  </ul>
</div>