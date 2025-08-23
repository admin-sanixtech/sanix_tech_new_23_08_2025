
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Courses Dropdown with Right-Side Sublist</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/front_page_navbar_styles.css">
</head>
<body>

<nav>
    <ul class="nav-menu">
        <li class="dropdown position-relative">
        <li><a href="https://www.sanixtech.in/DEV/index.php">Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Services</a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.sanixtech.in/DEV/web_development.php">Web Development</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/industry_st_projects.php">Industry Standard Projects</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/academic_projects.php">Acedemic Projects</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/data_migration.php">Data Migration</a></li>
                </ul>
            </li>
         <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Courses</a>
                <ul class="dropdown-menu">
                     <li class="dropdown-submenu">
                         <div class="submenu-toggle">
                            <a href="https://www.sanixtech.in/DEV/python_course.php">Python Programming</a>
                            <button class="arrow-btn" data-target="submenu-python">
                                <i class="bi bi-caret-right-fill"></i>
                            </button>
                            <ul class="submenu" id="submenu-python">
                                <li><a href="https://www.sanixtech.in/DEV/python_course.php">Overview</a></li>
                                <li><a href="https://www.sanixtech.in/DEV/python_projects.php">Projects</a></li>
                                <li><a href="https://www.sanixtech.in/DEV/python_interview.php">Interview Prep</a></li>
                             </ul>
                        </div>    
            </li>
            <li><a href="https://www.sanixtech.in/DEV/datascience_course.php">Data Science</a></li>
            
            <li class="dropdown-submenu">
              <a href="#" class="submenu-toggle">Web Development <i class="bi bi-caret-right-fill"></i></a>
              <ul class="submenu">
                <li><a href="#">HTML</a></li>
                <li><a href="#">CSS</a></li>
                <li class="dropdown-submenu">
                  <a href="#">JavaScript <i class="bi bi-caret-right-fill"></i></a>
                  <ul class="submenu">
                    <li><a href="https://www.sanixtech.in/DEV/angular_course.php">Angular</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/reactjs_course.php">React JS</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/nodejs_course.php">Node JS</a></li>
                  </ul>
                </li>
              </ul>
            </li>


           <li><a href="https://www.sanixtech.in/DEV/machine_learning.php">Machine Learning</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects</a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.sanixtech.in/DEV/python_course.php">Python Programming</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/datascience_projects.php">Data Science</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/web_development.php">Web Development</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/machine_learning_projects.php">Machine Learning</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Interview Preparation</a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.sanixtech.in/DEV/mock_interviews.php">Mock Interview</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/data_engineer_interview_prep.php">Data Engineering</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/datascience_interview_prep.php">Data Science</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/web_development.php">Web Development</a></li>
                    <li><a href="https://www.sanixtech.in/DEV/machine_learning_interview_prep.php">Machine Learning</a></li>
                </ul>
            </li>

            <li><a href="https://www.sanixtech.in/DEV/careers.php">Careers</a></li>
            <li><a href="https://www.sanixtech.in/DEV/about_us.php">About Us</a></li>
            <li><a href="https://www.sanixtech.in/DEV/contact.php">Contact Us</a></li>
</li>     
        </ul>
</nav>
<script>
 /* document.querySelectorAll('.arrow-btn').forEach(button => {
    button.addEventListener('click', function (e) {
      e.stopPropagation(); // prevent dropdown from closing
      const targetId = this.getAttribute('data-target');
      const submenu = document.getElementById(targetId);

      // Toggle visibility
      if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
        this.innerHTML = '<i class="bi bi-caret-right-fill"></i>';
      } else {
        // Close all submenus first
        document.querySelectorAll('.submenu').forEach(menu => {
          menu.style.display = 'none';
        });
        document.querySelectorAll('.arrow-btn').forEach(btn => {
          btn.innerHTML = '<i class="bi bi-caret-right-fill"></i>';
        });

        submenu.style.display = 'block';
        this.innerHTML = '<i class="bi bi-caret-down-fill"></i>';
      }
    });
  }); */

  /* visible all nested item from here */
  document.querySelectorAll('.arrow-btn').forEach(button => {
  button.addEventListener('click', function (e) {
    e.stopPropagation(); // prevent dropdown from closing
    const targetId = this.getAttribute('data-target');
    const submenu = document.getElementById(targetId);

    // Toggle visibility of clicked submenu
    const isVisible = submenu.style.display === 'block';

    // Close only sibling submenus (not all)
    const parent = this.closest('.dropdown-submenu');
    if (parent) {
      parent.querySelectorAll('.submenu').forEach(menu => {
        menu.style.display = 'none';
      });
      parent.querySelectorAll('.arrow-btn').forEach(btn => {
        btn.innerHTML = '<i class="bi bi-caret-right-fill"></i>';
      });
    }

    // Toggle current one
    submenu.style.display = isVisible ? 'none' : 'block';
    this.innerHTML = isVisible
      ? '<i class="bi bi-caret-right-fill"></i>'
      : '<i class="bi bi-caret-down-fill"></i>';
  });
}); 

  </script>

  <script>
  document.querySelectorAll('.submenu-toggle').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault(); // prevent default if link is '#'
    });
  });
</script>




</body>
</html>
