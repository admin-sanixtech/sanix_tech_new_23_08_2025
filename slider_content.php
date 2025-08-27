
<?php

function slider_content_fun(){

    echo'<section class="header-slider-area">
			<div id="carousel-example-generic" class="carousel slide carousel-fade" data-ride="carousel">';
			
			  #<!-- Indicators -->
				echo'<ol class="carousel-indicators">
					<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
					<li data-target="#carousel-example-generic" data-slide-to="1"></li>
					<li data-target="#carousel-example-generic" data-slide-to="2"></li>
				</ol>';

				#<!-- Wrapper for slides -->
				echo'<div class="carousel-inner" role="listbox">
					<div class="item active">
						<div class="single-slide-item slide-1">
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<div class="single-slide-item-content">
											<h2>Consult Your <br> Business With Us</h2>
											<p>
												We are the unique Consultancy Farm for your business solution,
                                                That is ready to take challenge and knockout your business problems. 
											</p>
											<button type="button" class="slide-btn">get started	</button>
											<button type="button"  class="slide-btn">explore more</button>
											
										</div>';#<!-- /.single-slide-item-content-->
									echo'</div>';#<!-- /.col-->
								echo'</div>';#<!-- /.row-->
							echo'</div>';#<!-- /.container-->
                        echo'</div>';#<!-- /.single-slide-item-->
					echo'</div>';#<!-- /.item .active-->
						echo'<div class="item">
						<div class="single-slide-item slide-2">
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<div class="single-slide-item-content">
											<h2>
												Consult Your <br> Business
											</h2>
											<p>
												Get a custom solution developed tailored to your requirements to elevate your business profits.
											</p>
											<button type="button"  class="slide-btn">
												get started
											</button>
											<button type="button"  class="slide-btn">
												explore more
											</button>
										</div>';#<!-- /.single-slide-item-content-->
									
										echo'</div>';#<!-- /.col-->
									echo'</div>';#<!-- /.row-->
								echo'</div>';#<!-- /.container-->
							echo'</div>';#<!-- /.single-slide-item-->
						echo'</div>';#<!-- /.item .active-->
						echo'<div class="item">
						<div class="single-slide-item slide-2">
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<div class="single-slide-item-content">
											<h2>
												Consult Your <br> Business
											</h2>
											<p>
												Get a custom solution developed tailored to your requirements to elevate your business profits.
											</p>
											<button type="button"  class="slide-btn">
												get started
											</button>
											<button type="button"  class="slide-btn">
												explore more
											</button>
										</div>';#<!-- /.single-slide-item-content-->
									
										echo'</div>';#<!-- /.col-->
									echo'</div>';#<!-- /.row-->
								echo'</div>';#<!-- /.container-->
							echo'</div>';#<!-- /.single-slide-item-->
						echo'</div>';#<!-- /.item .active-->
					echo'</div>';#<!-- /.carousel-inner-->

				#<!-- Controls -->
				echo'<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
					<span class="lnr lnr-chevron-left"></span>
				</a>
				<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
					<span class="lnr lnr-chevron-right"></span>
				</a>
			</div>';#<!-- /.carousel-->

			echo'</section>';#<!-- /.header-slider-area-->
		


}

?>