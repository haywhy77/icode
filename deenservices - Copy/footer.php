	<section class="w3l-footer-17">
		<div class="footer17_sur">
			<div class="wrapper">
				<div class="footer17-top">
					<div class="footer17-top-left1_sur">
						<h6>Job Updates</h6>
						<p>Are you interested in jobs, need to get latest updates and information?</p>
						<form action="#" class="subscribe" method="post">
							<input type="email" name="email" placeholder="Enter email" required="">
							<button><span class="fa fa-paper-plane"></span></button>
						</form>
					</div>
					<div class="footer17-top-left2_sur">
						<h6>Job Categories</h6>
						<ul>
							<li><a href="jobs.php">Healthcare Assistant</a></li>
							<li><a href="jobs.php">Home Support</a></li>
							<li><a href="jobs.php">Carer</a></li>
							<li><a href="jobs.php">Customer Care</a></li>
						</ul>
					</div>
					<div class="footer17-top-left3_sur">
						<h6>Job Locations</h6>
						<ul>
							<li><a href="#url">Southwark</a></li>
							<li><a href="#url">Lewisham</a></li>
							<li><a href="#url">Greenwich</a></li>
							<li><a href="#url">Dartford</a></li>
						</ul>
					</div>
					<div class="footer17-top-left4_sur">
						<h6>Hot Links</h6>
						<ul>
							<li><a href="about.php">About Us</a></li>
							<li><a href="services.php">Our Services</a></li>
							<li><a href="contact.php">Contact Us</a></li>
							<li><a href="jobs.php">Job Updates</a></li>
						</ul>

					</div>
					<div class="footer17-top-left5_sur">
						<h6>Mobile App Coming Soon</h6>
						<a href="#playstore"><img src="assets/images/googleplay.png" class="img-responsive" alt=""></a>
						<a href="#appstore"><img src="assets/images/appstore.png" class="img-responsive" alt=""></a>
					</div>
				</div>
				<div class="footer-left">
					<p>© <?php echo date('Y'); ?> Deen Consult Services | <a href="assets/websites-privacy-cookie-policy.pdf" target="new">Privacy Policy</a> | Powered by
                    <a href="http://icoderesources.com.ng/">iCode Resources</a></p>
				</div>
			</div>
		</div>
	</section>
	<script type="text/javascript" src="dashboard/ui/js/vendors/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="dashboard/ui/js/vendors/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="dashboard/ui/js/vendors/bootstrap/bootstrap.bundle.min.js"></script>

	<script type="text/javascript" src="dashboard/ui/js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<!-- move top -->
	<button onclick="topFunction()" id="movetop" title="Go to top">
		<span class="fa fa-angle-up"></span>
	</button>
	<script>
		// When the user scrolls down 20px from the top of the document, show the button
		window.onscroll = function () {
			scrollFunction()
		};

		function scrollFunction() {
			if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
				document.getElementById("movetop").style.display = "block";
			} else {
				document.getElementById("movetop").style.display = "none";
			}
		}

		// When the user clicks on the button, scroll to the top of the document
		function topFunction() {
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		}
	</script>
	<!-- /move top -->
	<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="true"></script>
<script>
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#000000",
      "text": "#44C8F4"
    },
    "button": {
      "background": "#F73333",
      "text": "#ffffff"
    }
  },
  "showLink": false,
  "type": "opt-out",
  "content": {
    "message": "Deen Consult Services website uses cookies to ensure you get the best experience on our website.",
    "dismiss": "Accept Cookies",
    "deny": "Decline Cookies"
  }
});
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/623b10212bd26d087e744ec1/1furbe15b';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>

</html>