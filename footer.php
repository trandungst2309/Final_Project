	<?php
	include_once 'header.php';
	?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
	    <meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" href="css/footer.css">

	    <title>footer</title>
	</head>
	<style>
		.copy-right-sec{
			background-color: black !important;
			color: white !important;
		}
	</style>

	<body>

	</body>

	</html>

	<!-- footer section start -->
	<footer id="footer">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-3">
	                <a href="homepage.php"><img src="image/TDicon.png" alt=""
	                        class="img-fluid logo-footer"></a>
	                <div class="footer-about">
	                    <p>We always accompany you on every trip. We are committed to providing services and support
							 policies that bring new and enthusiastic experiences </p>
	                </div>

	            </div>
	            <div class="col-md-3">
	                <div class="useful-link">
	                    <h2>More</h2>
	                    <img src="./assets/images/about/home_line.png" alt="" class="img-fluid">
	                    <div class="use-links">
	                        <li><a href="homepage.php"><i class="fa-solid fa-angles-right"></i> Home</a></li>
	                        <li><a href="gallery.php"><i class="fa-solid fa-angles-right"></i> Gallery</a></li>
	                        <li><a href="about.php"><i class="fa-solid fa-angles-right"></i> About us</a></li>
	                    </div>
	                </div>

	            </div>
	            <div class="col-md-3">
	                <div class="social-links">
	                    <h2>Follow Us</h2>
	                    <img src="./assets/images/about/home_line.png" alt="">
	                    <div class="social-icons">
	                        <li><a href="#"><i class="fa-brands fa-facebook-f"></i> Facebook</a></li>
	                        <li><a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a></li>
	                        <li><a href="#"><i class="fa-brands fa-youtube"></i> Youtube</a></li>
	                    </div>
	                </div>
	                <br>

	                <form action="send_message.php" method="post">
	                    <div class="mb-3">
	                        <label for="name" class="form-label">Name</label>
	                        <input type="text" class="form-control" id="name" name="name" required>
	                    </div>
	                    <div class="mb-3">
	                        <label for="message" class="form-label">Message</label>
	                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
	                    </div>
	                    <div class="text-center">
	                        <button type="submit" class="btn btn-primary">Send Message</button>
	                    </div>
	                </form>

	            </div>
	            <div class="col-md-3">
	                <div class="address">
	                    <h2>Address</h2>
	                    <img src="./assets/images/about/home_line.png" alt="" class="img-fluid">
	                    <div class="address-links">
	                        <li class="address1"><i class="fa-solid fa-location-dot"></i> Can Tho facility: No. 160, 30/4
	                            Street, An Phu, Ninh Kieu, Can Tho City
	                        </li>
	                        <li><a href=""><i class="fa-solid fa-phone"></i> (+84)397655583</a></li>
	                        <li><a href=""><i class="fa-solid fa-envelope"></i> dungttgcc200402@fpt.edu.vn</a></li>
	                    </div>
	                </div>
	            </div>

	        </div>
	    </div>

	</footer>
	<!-- footer section end -->
	<!-- footer copy right section start -->
	<section id="copy-right">
	    <div class="copy-right-sec"><i class="fa-solid fa-copyright"></i>
	        2025	 TD Motor | All Rights Reserved

	    </div>

	</section>
	<!-- footer copy right section end -->