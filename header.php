</div>
<?php
session_start();
// var_dump($_SESSION);exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>DeenConsult | Contact </title>
	<link rel="stylesheet" href="dashboard/ui/css/styles.css">
	<link rel="stylesheet" href="assets/css/style-freedom.css">
    <link rel="icon" href="assets/images/logo2.png">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />

</head>

<body>
	<div class="w3l-headers-9">
		<header>
			<div class="wrapper">
				<div class="header">

					<div>
						<!-- <a href="index.php" class="logo">HR Crew</a> -->
						<!-- if logo is image enable this   -->
						<a class="logo" href="index.php">
							<img src="assets/images/logo.png" alt="Deen Consult Logo" title="Deen Consult Logo" style="width:150px; margin-top: 0 !important;" />
						</a>
					</div>
					<div class="bottom-menu-content">
						<input type="checkbox" id="nav" />
						<label for="nav" class="menu-bar"></label>
						<nav>
							<ul class="menu">
								<li class="link-nav active"><a href="index.php" class="link-nav active">Home</a></li>
								<li><a href="about.php" class="link-nav">About</a></li>
								<li><a href="services.php" class="link-nav">Services</a></li>
								<li><a  href="jobs.php" class="link-nav">Job Openings </a></li>
								<li>
									<label for="drop-4" class="toggle toogle-2">Employers </label>
									<a href="#" class="link-nav dropdown-toggle">Employers </a>
									<input type="checkbox" id="drop-4" />
									<ul>
										<li><a href="./dashboard/client">Login Account</a></li>
										<li><a href="./dashboard/client/signup">Signup Account</a></li>
									</ul>
								</li>
								<li><a href="contact.php" class="link-nav">Contact Us</a></li>
								<li class="notLogin"><a href="./dashboard/signup" class="jobs">Signup</a></li>
								<li class="notLogin"><a href="./dashboard/" class="jobs">Applicant Login</a></li>
								<li class="isLogin hide"><a href="./dashboard" class="jobs">My Account</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</header>
	</div>

	<section class="w3l-inner-banner">
		<div class="wrapper">
		</div>
	</section>


<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
		$.ajax({
			url: "dashboard/candidate/validate",
			type: 'GET',
			processData: false,
			contentType: false,
			beforeSend: function () {
				// alert('deji')
			},
			complete: function(){
				
			},
			success: function(data){
				if(!data.status){
					$(".isLogin").removeClass('show').addClass('hide');
					$(".notLogin").removeClass('hide').addClass('show');
				}else{
					$(".notLogin").removeClass('show').addClass('hide');
					$(".isLogin").removeClass('hide').addClass('show')
				}
			},
			error: function(error){
				console.log(error)
				$(".isLogin").removeClass('show').addClass('hide');
				$(".notLogin").removeClass('hide').addClass('show');
			},
		});
    })
</script>