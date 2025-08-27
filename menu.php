<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php

function menu_content()
{
    echo '<section id="menu">
			<div class="container">
				<div class="menubar">
					<nav class="navbar navbar-default">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="index.php">
								<h4>Sanix Technology</h4>
							</a>
						</div>';

    echo '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
							<ul class="nav navbar-nav navbar-right">
								<li><a href="index.php">Home</a></li>
								<li><a href="service.php">Service</a></li>	
								<li class="dropdown">
									<a href="courses.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Courses <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="python_course.php">Python</a></li>
										<li><a href="html_course.php">HTML</a></li>
										<li><a href="css_course.php">CSS</a></li>
										<li><a href="mysql_course.php">MySQL</a></li>
									</ul>
								</li>
								<li><a href="contact.php">Contact</a></li>
								<li><a href="about.php">About</a></li>
							</ul>
						</div>';

    echo '</nav>
				</div>
			</div>
		</section>';
}

?>
