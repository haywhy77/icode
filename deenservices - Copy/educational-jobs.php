
<!-- header -->

<?php 
include ('header.php'); 
?>
    <section class="w3l-breadcrumns">
        <div class="wrapper">
            <ul>
                <li><a href="index.html">Home</a> <span class="fa fa-angle-double-right"></span></li>
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
                        <h5 class="card-title">Educational Jobs </h5>
                        <div class="w3l-price-2 job_contents">
                            <!-- <div class="price-box btn-layout bt6">
                                <div class="grid grid-column-2">
                                    <div class="column1">
                                        <a href="#img5">
                                            <img src="assets/images/job4.png" width="60px" alt=""
                                                class="img-responsive" />
                                        </a>
                                        <div class="job-info">
                                            <h6 class="pricehead"><a href="#link">Web designer</a></h6>
                                            <ul class="location">
                                                <li><span class="fa fa-map-marker"></span> London</li>
                                                <li><span class="fa fa-briefcase"></span> Web designer</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="column2">
                                        <p><Strong>Type :</Strong>Fulltime</p>
                                        <p><Strong>Time :</Strong>7 days ago</p>
                                    </div>
                                    <div class="column3 text-right">
                                        <a href="job-single.html" class="actionbg">Apply Now</a>
                                    </div>
                                </div>
                            </div> -->
                            <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                <img src="assets/loading.gif" style="width: 20%;" />
                            </div>
                        </div>
                    </div>
                    <!-- left side blog post content -->

                    <!-- right side bar -->
                    <div class="right-side-bar">
                        <aside class="posts single-left-inner">
                            <h5 class="search">Search by keywords</h5>
                            <form class="form-inline search-form" action="#" method="post">
                                <input class="form-control" type="search" placeholder="Search here" aria-label="email"
                                    required="">
                                <button class="btn text-wh mt-3 w-100" type="submit"><span
                                        class="fa fa-search"></span></button>
                            </form>
                        </aside>
                        <aside class="posts single-left-inner">
                            <h5 class="search">Jobs qualification</h5>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Bachelor of science</label>
                            </div>
                            <div class="brand-equal ">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Master degree</label>
                            </div>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">PHD</label>
                            </div>
                        </aside>
                        <aside class="posts single-left-inner">
                            <h5 class="search">Job level</h5>
                            <div class="brand-equal ">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Project Manager</label>
                            </div>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Jr. Officer / Sr. officer</label>
                            </div>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Team leader</label>
                            </div>
                        </aside>
                        <aside class="posts single-left-inner">
                            <h5 class="search">Job Skills</h5>
                            <div class="brand-equal ">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Html5, CSS3, Bootstrap</label>
                            </div>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Javascript</label>
                            </div>
                        </aside>
                        <aside class="posts single-left-inner">
                            <h5 class="search">Job shift</h5>
                            <div class="brand-equal ">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Morning / Afternoon shift</label>
                            </div>
                            <div class="brand-equal">
                                <input type="checkbox" class="checked">
                                <label class="brand-name">Evening / Night shift</label>
                            </div>
                        </aside>
                    </div>
                    
                    <!-- //right side bar -->
                </div>
            </div>
        </div>
    </section>
    <!-- //blog single-->
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            var url="dashboard/candidate/jobs/category/education"
            $.get( url )
            .done(function( data ) {
                if(data.status){
                    var payload=data.payload.data;
                    
                    var content= Object.entries(payload).map((item)=>{
                        // console.log("Item: ", item[1])
                        var date1 = new Date();
                        var date2 = new Date(item[1].created_at);
                        var diffDays = parseInt((date1 - date2) / (1000 * 60 * 60 * 24), 10); 
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
                                        <p><Strong>Time :</Strong>${diffDays} days ago</p>
                                    </div>
                                    <div class="column3 text-right">
                                        <a href="apply.php?id=${btoa(item[1].id)}" class="actionbg">Apply Now</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $(".job_contents").html(content);
                }else{
                    $(".job_contents").html("No job openings currently.");
                }
            })
            .fail(function(error) {
                console.log("Error: ", error);
            })
            .always(function() {
                // $(".job_contents").html("No job openings currently.");
            });
        })
            
    </script>
    
    <!-- footer17 -->
<?php include ('footer.php'); ?>