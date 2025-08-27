<?php
function footer_copyright_content_fun() {

    echo'<footer class="footer-copyright">
			<div class="container">
				<div class="row">
					<div class="col-sm-7">
						<div class="foot-copyright pull-left">
							<p>
								&copy; All Rights Reserved. Designed and Developed by
							 	<a href="index.php">Sanix Technology</a>
							</p>
						</div>';#<!--/.foot-copyright-->
						echo'</div>';#<!--/.col-->
					echo'<div class="col-sm-5">
						<div class="foot-menu pull-right">	  
							<ul>
								<li ><a href="#">legal</a></li>
								<li ><a href="#">sitemap</a></li>
								<li ><a href="#">privacy policy</a></li>
							</ul>
						</div>';#<!-- /.foot-menu-->
						echo'</div>';#<!--/.col-->
					echo'</div>';#<!--/.row-->
				echo'<div id="scroll-Top">
					<i class="fa fa-angle-double-up return-to-top" id="scroll-top" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to Top" aria-hidden="true"></i>';
					echo'</div>';#<!--/.scroll-Top-->
				echo'</div>';#<!-- /.container-->

		echo'</footer>';#<!-- /.footer-copyright-->
		

}


function footer_content_fun(){
	echo'<section class="hm-footer">
			<div class="container">
				<div class="hm-footer-details">
					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12">
							<div class="hm-footer-widget">
								<div class="hm-foot-title ">
									<div class="logo">
										<a href="index.php">';
											#<!-- <img src="assets/images/logo/logo.png" alt="logo" /> -->
											echo'<h1>Sanix Technology</h1>
										</a>
									</div>';#<!-- /.logo-->
									echo'</div>';#<!--/.hm-foot-title-->
									echo'<div class="hm-foot-para">
									<p>
										Lorem ipsum dolor sit amt conetur adcing elit. Sed do eiusod tempor utslr. Ut laboris nisi ut aute irure dolor in rein velit esse.
									</p>
								</div>';#<!--/.hm-foot-para-->
								echo'<div class="hm-foot-icon">
									<ul>
										<li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li><!--/li-->
										<li><a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li><!--/li-->
										<li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li><!--/li-->
										<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li><!--/li-->
									</ul>';#<!--/ul-->
									echo'</div>';#<!--/.hm-foot-icon-->
									echo'</div>';#<!--/.hm-footer-widget-->
									echo'</div>';#<!--/.col-->
									echo'<div class=" col-md-2 col-sm-6 col-xs-12">
							<div class="hm-footer-widget">
								<div class="hm-foot-title">
									<h4>Useful Links</h4>
								</div>';#<!--/.hm-foot-title-->
								echo'<div class="footer-menu ">	  
									<ul class="">
										<li><a href="index.php" >Home</a></li>
										<li><a href="about.php">About</a></li>
										<li><a href="services.php">Service</a></li>
										<li><a href="portfolio.php">Portfolio</a></li>
										<li><a href="blog.php">Blog</a></li>
										<li><a href="contact.php">Contact us</a></li> 
									</ul>
								</div>';#<!-- /.footer-menu-->
								echo'</div>';#!--/.hm-footer-widget-->
								echo'</div>';#<!--/.col-->
								echo'<div class=" col-md-3 col-sm-6 col-xs-12">
							<div class="hm-footer-widget">
								<div class="hm-foot-title">
									<h4>from the news</h4>
								</div>';#<!--/.hm-foot-title-->
								echo'<div class="hm-para-news">
									<a href="blog_single.php">
										The Pros and Cons of Starting an Online Business.
									</a>
									<span>12th June 2017</span>
								</div>';#<!--/.hm-para-news-->
								echo'<div class="footer-line">
									<div class="border-bottom"></div>
								</div>
								<div class="hm-para-news">
									<a href="blog_single.php">
										The Pros and Cons of Starting an Online Business.
									</a>
									<span>12th June 2017</span>
								</div>';#<!--/.hm-para-news-->
								echo'</div>';#<!--/.hm-footer-widget-->
								echo'</div>';#<!--/.col-->
								echo'<div class=" col-md-3 col-sm-6  col-xs-12">
							<div class="hm-footer-widget">
								<div class="hm-foot-title">
									<h4> Our Newsletter</h4>
								</div>';#<!--/.hm-foot-title-->
								echo'<div class="hm-foot-para">
									<p class="para-news">
										Subscribe to our newsletter to get the latest News and offers..
									</p>
								</div>';#<!--/.hm-foot-para-->
								echo'<div class="hm-foot-email">
									<div class="foot-email-box">
										<input type="text" class="form-control" placeholder="Email Address">
									</div>';#<!--/.foot-email-box-->
									echo'<div class="foot-email-subscribe">
										<button type="button" >go</button>
									</div>';#<!--/.foot-email-icon-->
									echo'</div>';#<!--/.hm-foot-email-->
			echo'				</div>';#<!--/.hm-footer-widget-->
			echo'			</div>';#<!--/.col-->
			echo'		</div>';#<!--/.row-->
			echo'	</div>';#<!--/.hm-footer-details-->
			echo'</div>';#<!--/.container-->

			echo'</section>';#<!--/.hm-footer-details-->


}

?>