
<?php include ('header.php'); ?>


	<section class="w3l-cover-3">
		<div class="cover top-bottom">
			<div class="wrapper">
				<div class="middle-section text-center">
					<div class="section-width">
						<h2>Searching For Your Dream Jobs</h2>
						<p>We recruit for permanent and temporary positions</p>
						<div class="most-searches">
							<h4>Most Searches</h4>
							<ul>
								<li><a href="jobs.php">Healthcare Jobs</a></li>
								<li><a href="job.php">Education Jobs</a></li>
								<li><a href="job.php">Customer Service</a></li>
							</ul>
						</div>
						<form action="jobs.php" class="w3l-cover-3-gd" method="post">
							<input type="search" name="text" placeholder="Search keyword.." required>
							<span class="input-group-btn">
								<select class="btn btn-default" name="ext" required>
									<option selected="">Select Category</option>
									<!--<option>Designing</option>-->
									<!--<option>Development</option>-->
									<!--<option>Software developer</option>-->
									<!--<option>.net developer</option>-->
									<!--<option>Customer service</option>-->
									<!--<option>Human Resource</option>-->
									<!--<option>IT (CSE)</option>-->
								</select>
							</span>
							<span class="input-group-btn">
								<select class="btn btn-default" name="ext" required>
									<option selected="">Select Country</option>
									<!--<option>Australia</option>-->
									<!--<option>London</option>-->
									<!--<option>India</option>-->
									<!--<option>Bangladesh</option>-->
									<!--<option>Saudi Arabia</option>-->
									<!--<option>America</option>-->
									<!--<option>Srilanka</option>-->
								</select>
							</span>
							<button type="submit">Search</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- companies20 -->
	<!--<section class="w3l-companies-20">-->
	<!--	<div class="companies20">-->
	<!--		<h3 class="heading">Explore Our Services</h3>-->
	<!--		<div class="wrapper">-->
	<!--			<div class="companies20-content">-->
	<!--				<div class="companies-wrapper">-->
	<!--					<div class="owl-carousel owl-theme">-->
	<!--						<div class="item">-->
	<!--							<a href="services.php">Staffing & Recruitment</a>-->
	<!--							<p>- Healthcare Staffing</p>-->
	<!--							<p>- Education Staffing-->
	<!--							<span class="pos-icon">-->
	<!--								<span class="fa fa-laptop"></span>-->
	<!--							</span>-->
	<!--						</div>-->
	<!--						<div class="item">-->
	<!--							<a href="services.php">Healthcare Services</a>-->
	<!--							<p>- Supported Living</p>-->
	<!--							<p>- Domicilliary Care</p>-->
	<!--							<span class="pos-icon">-->
	<!--								<span class="fa fa-headphones"></span>-->
	<!--							</span>-->
	<!--						</div>-->
	<!--						<div class="item">-->
	<!--							<a href="services.php">Career Development </a>-->
	<!--							<p>- Job Trainings</p>-->
	<!--							<p>- Organizational Trainings</p>-->
	<!--							<span class="pos-icon">-->
	<!--								<span class="fa fa-tablet"></span>-->
	<!--							</span>-->
	<!--						</div>-->
							<!--<div class="item">-->
							<!--	<a href="services.php">Organogramme</a>-->
							<!--	<p>- Corporate Organogramme</p>-->
							<!--	<p>- Corporate Structuring</p>-->
							<!--	<span class="pos-icon">-->
							<!--		<span class="fa fa-cogs"></span>-->
							<!--	</span>-->
							<!--</div>-->

	<!--					</div>-->
	<!--				</div>-->
	<!--			</div>-->
	<!--		</div>-->
	<!--	</div>-->
	<!--</section>-->
	<!-- //companies20 -->

    <div class="w3l-grids-block-5">
        <!-- grids block 5 -->
        <section id="grids5-block">
            <h3 class="heading">Explore Our Services</h3>
            <div class="wrapper">
                <div class="d-grid">
                    <div class="grids5-info">
                        <div class="blog-info">
                            <h4><a href="services.php">Staffing and Recruitment</a></h4>
                            <p>We provide recruitment services:</p>
                            <p>- Healthcare (Temporary/Permanent)</p>
                            <p>- Education (Temporary/Permanent)</p>
                        </div>
                    </div>
                    <div class="grids5-info">
                        <div class="blog-info">
                            <h4><a href="services.php">Healthcare Services</a></h4>
                            <p>- Supported Living</p>
                            <p>- Care Homes</p>
                            <p>- Domiciliary Care</p>
                        </div>
                    </div>
                    <div class="grids5-info">
                        <div class="blog-info">
                            <h4><a href="services.php">Training & Career Development</a></h4>
                            <p>We give consulting services in Trainings and Career Development to organizations to help improve staff performance.</p>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
    </div>


	<!-- w3l-covers-14 -->
	<section class="w3l-covers-14">
		<div id="covers14-block">
			<div class="wrapper">
				<div class="covers14-text">
					<h4>Your Dream Jobs Are Waiting </h4>
					<p>Over 12000 interactions, 4000+ success stories...make yours now.</p>
					<a class="actionbg" href="jobs.php">Apply Job</a>
					<a class="actionbg1" href="jobs.php">Search Jobs</a>
				</div>
			</div>
		</div>
	</section>
	<!-- w3l-covers-14 -->
	<section class="w3l-price-2">
		<div class="price-main">
			<h3 class="heading">Featured Jobs</h3>
			<div class="wrapper">
				<div class="pricing-style-w3ls">
					<div id="monthly" class="">
						<div class="pricing-chart">
							<div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                <img src="assets/loading.gif" style="width: 20%;" />
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>





	<script>
		var e = document.getElementById("filt-monthly"),
			d = document.getElementById("filt-hourly"),
			t = document.getElementById("switcher"),
			m = document.getElementById("monthly"),
			y = document.getElementById("hourly");

		e.addEventListener("click", function () {
			t.checked = false;
			e.classList.add("toggler--is-active");
			d.classList.remove("toggler--is-active");
			m.classList.remove("hide");
			y.classList.add("hide");
		});

		d.addEventListener("click", function () {
			t.checked = true;
			d.classList.add("toggler--is-active");
			e.classList.remove("toggler--is-active");
			m.classList.add("hide");
			y.classList.remove("hide");
		});

		t.addEventListener("click", function () {
			d.classList.toggle("toggler--is-active");
			e.classList.toggle("toggler--is-active");
			m.classList.toggle("hide");
			y.classList.toggle("hide");
		})
	</script>
	<!-- w3l-content-photo-5 -->
	<div class="w3l-content-photo-5">
		<div class="content">
			<h3 class="heading">Our Recruitment Process</h3>

			<div class="wrapper">
				<img src="assets/images/processs.png" align="center" class="img-responsive" alt="content-photo">
			</div>
		</div>
	</div>
	<!-- //w3l-content-photo-5 -->


	<div class="w3l-grids-block-5">
		<!-- grids block 5 -->
		<section id="grids5-block">
			<h3 class="heading">Career Tips</h3>
			<div class="wrapper">
				<div class="d-grid">
					<div class="grids5-info">
						<a href="services.php"><img src="assets/images/blog1.jpg" alt="" /></a>
						<div class="blog-info">
							<h5>June 1, 2024.</h5>
							<h4><a href="services.php">Never Stop Learning</a></h4>
							<p>To grow in your career, continous learning process is required to acquire updated and more knowledge in your field. Never stop learning, always research a better way to work.</p>
						</div>
					</div>
					<div class="grids5-info">
						<a href="services.php"><img src="assets/images/blog2.jpg" alt="" /></a>
						<div class="blog-info">
							<h5>June 1, 2024.</h5>
							<h4><a href="services.php">Work on your goals</a></h4>
							<p>Priotize your goals and work on the important ones first. Work on your assignments promptly and show your boss you have enthusiasm for what you’re doing.</p>
						</div>
					</div>
					<div class="grids5-info">
						<a href="services.php"><img src="assets/images/blog3.jpg" alt="" /></a>
						<div class="blog-info">
							<h5>June 1, 2024.</h5>
							<h4><a href="services.php">Be a team player</a></h4>
							<p>Learn how to work well alongside other people in your team. Being a team player raises how people respect you, and at the same time, builds a strong network and relationship with your co-workers.</p>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<!-- // grids block 5 -->

	<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            var url="dashboard/candidate/jobs/get-jobs"
                
			$.ajax({
				url: url,
				type: 'POST',
				data: {category: 'ALL'},
				processData: false,
				contentType: false,
				success: function(data){
					if(data.status){
				var payload=data.payload.data;
				
				var content= Object.entries(payload).map((item)=>{
					// console.log("Item: ", item[1])
					var diffDays='Ongoing';
                    var appClosed=true;
                    if(item[1].job_close_date !=='Ongoing'){
                        var today= new Date();
                        var date1 = new Date(item[1].job_close_date);
                        var date2 = new Date(item[1].created_at);
                        
                        if(item[1].job_close_date){
                            if(date1<today){
                                diffDays = "Application Closed";
                                appClosed=false;
                            }else{
                                diffDays = parseInt((date1 - date2) / (1000 * 60 * 60 * 24), 10) + ' days left';
                            }
                        }
                    } 
					console.log(diffDays )
					return `
						<div class="price-box btn-layout bt6">
							<div class="grid grid-column-2">
								<div class="column1">
									<a href="#img5">
										<img src="assets/images/job4.png" width="60px" alt=""
											class="img-responsive" />
									</a>
									<div class="job-info">
										<h6 class="pricehead"><a href="#link">${item[1].job_title}</a></h6>
										<ul class="location">
											<li><span class="fa fa-map-marker"></span> ${item[1].location}</li>
											<li><span class="fa fa-briefcase"></span> ${item[1].job_title}</li>
										</ul>
									</div>
								</div>
								<div class="column2">
									<p><Strong>Type :</Strong>${item[1].job_title}</p>
									<p><Strong>Close :</Strong>${diffDays}</p>
								</div>
								${appClosed ? `
                                    <div class="column3 text-right">
                                        <a href="apply.php?id=${btoa(item[1].id)}" class="actionbg">Apply Now</a>
                                    </div>`:``
                                }
							</div>
						</div>
					`;
					});
						$(".pricing-chart").html(content);
					}else{
						$(".pricing-chart").html("No job openings currently.");
					}
				},
				error: function(error){
					console.log(error)
					// $('.btn-process').removeClass('disabled');
					// $('.btn-process').html('Login');
				},
			});
        })
            
    </script>
	<!-- footer17 -->
<?php include ('footer.php'); ?>
