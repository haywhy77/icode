
<!-- header -->

<?php 
include ('header.php'); 
?>
    <section class="w3l-breadcrumns">
        <div class="wrapper">
            <ul>
                <li><a href="index.php">Home</a> <span class="fa fa-angle-double-right"></span></li>
                <li>Others Jobs</li>
            </ul>
        </div>
    </section>
    <!-- Blog -->
    <section class="w3l-blog-single no-padding">
        <div class="single blog">
            <div class="wrapper">
                <div class="d-grid grid-colunm-2">
                    <!-- left side blog post content -->
                    <div class="single-left">
                        <h5 class="card-title">Job Openings </h5>
                        <div class="w3l-price-2 job_contents">
                            
                            <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                <img src="assets/loading.gif" style="width: 20%;" />
                            </div>
                        </div>
                    </div>
                    <!-- left side blog post content -->

                    <!-- right side bar -->
                    <div class="right-side-bar">
                        <form class="search-form" action="./dashboard/candidate/jobs/get-jobs" method="post">
                            <aside class="posts single-left-inner">
                                <h5 class="search">Search by keywords</h5>
                                <input class="form-control" type="search" placeholder="Search here" aria-label="email" name="job_title">
                            </aside>
                            <aside class="posts single-left-inner">
                                <h5 class="search">Jobs Category</h5>
                                <div class="brand-equal">
                                    <input type="radio" class="checked" checked value="ALL" name="category">
                                    <label class="brand-name">All Category</label>
                                </div>
                                <div class="brand-equal ">
                                    <input type="radio" class="checked" value="HEALTH" name="category">
                                    <label class="brand-name">Health Jobs</label>
                                </div>
                                <div class="brand-equal">
                                    <input type="radio" class="checked" value="EDUCATION" name="category">
                                    <label class="brand-name">Educational Jobs</label>
                                </div>
                            </aside>
                            <aside class="posts single-left-inner">
                                <h5 class="search">Jobs Qualification</h5>
                                <div class="brand-equal">
                                    <input type="checkbox" class="checked" value="BACHELLOR DEGREE" name="qualifications[]">
                                    <label class="brand-name">Bachelor of Science</label>
                                </div>
                                <div class="brand-equal ">
                                    <input type="checkbox" class="checked" value="MASTER DEGREE" name="qualifications[]">
                                    <label class="brand-name">Master Degree</label>
                                </div>
                                <div class="brand-equal">
                                    <input type="checkbox" class="checked" value="PHD" name="qualifications[]">
                                    <label class="brand-name">PhD</label>
                                </div>
                            </aside>
                            
                            <button class="btn text-wh mt-3 w-100" type="submit" style="border: 1px solid #ff4848; color: #ff4848; height: 40px; width: 100%; border-radius:5px;">
                                <span class="fa fa-search"></span> Search
                            </button>
                        </form>
                    </div>
                    
                    <!-- //right side bar -->
                </div>
            </div>
        </div>
    </section>
    <!-- //blog single-->
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            
            

            $(".search-form").submit(function( e ) {
                var url="dashboard/candidate/jobs/get-jobs"
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: new FormData( this ),
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('.btn-process').addClass('disabled');
                        $('.btn-process').html('Processing...');
                    },
                    complete: function(){
                        $('.btn-process').removeClass('disabled');
                        $('.btn-process').html('Login');
                    },
                    success: function(data){
                        if(data.status){
                            var payload=data.payload.data;
                            
                            var content= Object.entries(payload).map((item)=>{
                                console.log("Item: ", item[1])
                                
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
                                // console.log(diffDays )
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
                            $(".job_contents").html(content);
                        }else{
                            $(".job_contents").html("No job openings currently.");
                        }
                    },
                    error: function(error){
                        console.log(error)
                        // $('.btn-process').removeClass('disabled');
                        // $('.btn-process').html('Login');
                    },
                });
                e.preventDefault();
            });
            $(".search-form").trigger('submit')
        })
            
    </script>
    
    <!-- footer17 -->
<?php include ('footer.php'); ?>