<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* sublist styles starts here */

/* Common styles */
.submenu-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}
.submenu-toggle a {
  flex-grow: 1;
  text-decoration: none;
  color: inherit;
   display: inline-flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.submenu-toggle:hover {
  background-color: #f1f1f1;
}
.arrow-btn {
  background: none;
  border: none;
  padding: 0;
  margin-left: 15px;
  cursor: pointer;
  font-size: 0.9em;
  vertical-align: middle;
}
 .arrow-btn:focus {
    outline: none;
    box-shadow: none;
  }
/* Submenu styles */
.submenu {
  display: none;
  position: absolute;
  top: 0;
  left: 100%;
  margin-left: 1px;
  background: #86abd1;
  padding:0;
  margin: 0;
  list-style: none;
  border: 1px solid #ddd;
  min-width: 200px;
  z-index: 1000;
   box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.submenu .submenu {
  top: 0;
  left: 100%;
  
}
.submenu li a {
  display: block;
  padding: 5px 10px;
  color: #333;
  text-decoration: none;
}

.submenu li a:hover {
  background-color: #e2e6ea;
}
.dropdown-submenu {
  position: relative;
  
}
.dropdown-submenu:hover > .submenu {
  display: block;
}
.dropdown-submenu .submenu:hover {
  display: block;
}
.dropdown-submenu > .submenu {
  display: none;
  position: absolute;
  top: 0;
  left: 100%;
}
.dropdown-submenu:hover > .submenu-toggle i {
  transform: rotate(90deg);
  transition: transform 0.2s;
}
</style>
</head>
<nav>
        <ul class="nav-menu">
        <li><a href="https://www.sanixtech.in">Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Services</a>
                <ul class="dropdown-menu">

                    <!-- Core Services -->
                    <li class="dropdown-submenu position-relative">
                            <div class="submenu-toggle">
                              <a href="https://www.sanixtech.in/web_development.php">Web Development</a>
                                <i class="bi bi-caret-right-fill"></i>
                            </div>
                            <ul class="submenu" id="submenu-web">
                                <li><a href="https://www.sanixtech.in/ui_ux_design.php">UI/UX Design</a></li>
                                <li><a href="https://www.sanixtech.in/seo_marketing.php">SEO & Digital Marketing</a></li>
                                <li><a href="https://www.sanixtech.in/hosting_domain.php">Hosting & Domain Services</a></li>
                                 <li><a href="https://www.sanixtech.in/app_dev_maintenance.php">App Development & Maintenance</a></li>
                            </ul>

                    </li> 
                    <li class="dropdown-submenu position-relative">
                            <div class="submenu-toggle">
                              <a href="https://www.sanixtech.in/data_engineering.php">Data Engineering</a>
                                <i class="bi bi-caret-right-fill"></i>
                            </div>
                            <ul class="submenu" id="submenu-Pro">
                                <li><a href="https://www.sanixtech.in/data_migration.php">Data Migration Services</a></li>
                                <li><a href="https://www.sanixtech.in/trainings.php">Trainings</a></li>
                                <li><a href="https://www.sanixtech.in/academic_projects.php">Academic Projects</a></li>
                            </ul>

                     </li> 
                     <li class="dropdown-submenu position-relative">
                            <div class="submenu-toggle">
                              <a href="https://www.sanixtech.in/custom_solutions.php">Software Solutions</a>
                                <i class="bi bi-caret-right-fill"></i>
                            </div>
                            <ul class="submenu" id="submenu-Pro">
                                <li><a href="https://www.sanixtech.in/custom_solutions.php">Custom Software Solutions</a></li>
                                <li><a href="https://www.sanixtech.in/qa_engineering.php">QA & Software Testing</a></li>
                                <li><a href="https://www.sanixtech.in/product_engineering.php">Software Product Engineering</a></li>
                                <li><a href="https://www.sanixtech.in/internship_placement.php">Internship & Placement</a></li>                    
                                <li><a href="https://www.sanixtech.in/cybersecurity.php">Cybersecurity Services</a></li>
                                <li><a href="https://www.sanixtech.in/enterprise_integration.php">Enterprise Apps & Integrations</a></li>
                                <li><a href="https://www.sanixtech.in/generative_ai.php">Generative AI</a></li>
                            </ul>
                     </li> 
                     <li class="dropdown-submenu position-relative">
                            <div class="submenu-toggle">
                              <a href="https://www.sanixtech.in/data_engineering.php">Staffing</a>
                                <i class="bi bi-caret-right-fill"></i>
                            </div>
                            <ul class="submenu" id="submenu-Pro">

                              <li><a href="https://www.sanixtech.in/infrastructure.php">IT Infrastructure Services</a></li>
                              <li><a href="https://www.sanixtech.in/it_staffing.php">IT Staffing & Consulting</a></li>
                              <li><a href="https://www.sanixtech.in/professional_services.php">Professional Consulting Services</a></li>
                            </ul>

                     </li> 

                </ul>
            </li>
            <li><a href="https://www.sanixtech.in/category_cards_show.php">Learning Zone</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects</a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.sanixtech.in/industry_st_projects.php">Industry-grade Project Solutions</a></li>
                    <li><a href="https://www.sanixtech.in/academic_projects.php">Academic Project Support</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Interview Preparation</a>
                <ul class="dropdown-menu">
                    <li><a href="https://www.sanixtech.in/training_certification.php">Training & Certification</a></li>
                    <li><a href="https://www.sanixtech.in/mock_interviews.php">Mock Interviews & Career Help</a></li>
                    <li><a href="https://www.sanixtech.in/job_posts.php">Jobs</a></li>

                </ul>
            </li>

            <li><a href="careers.php">Careers</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
</nav>
<script>
     // Hide all other submenus
      const parent = button.closest('ul');
if (parent) {
  parent.querySelectorAll('.submenu').forEach(menu => {
    if (menu !== submenu) {
      menu.style.display = 'none';
    }
  });
}

      // Toggle current submenu
      submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';

      // Toggle arrow direction (optional)
      const icon = button.querySelector('i');
      if (submenu.style.display === 'block') {
        icon.classList.remove('bi-caret-right-fill');
        icon.classList.add('bi-caret-down-fill');
      } else {
        icon.classList.remove('bi-caret-down-fill');
        icon.classList.add('bi-caret-right-fill');
      }
   
</script> 