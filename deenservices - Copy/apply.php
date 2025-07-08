
<!-- header -->

<?php 
include ('header.php'); 
?>
    <section class="w3l-breadcrumns">
        <div class="wrapper">
            <ul>
                <li><a href="index.html">Home</a> <span class="fa fa-angle-double-right"></span></li>
                <li>Job Details</li>
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
                        
                        <div class="w3l-price-2 job_contents">
                            
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
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            var url=`dashboard/candidate/jobs/${id}`;
            $.get( url )
            .done(function( data ) {
                console.log(data);
                if(data.status){
                    var payload=data.payload.job;
                    var profile=data.payload.profile;
                    $("#cand_id").val(profile.id);
                    $("#job_id").val(payload.id);
                    var date1 = new Date();
                    var date2 = new Date(payload.created_at);
                    var diffDays = parseInt((date1 - date2) / (1000 * 60 * 60 * 24), 10); 
                    var isLogin=profile.id ? `
                        <button class="btn btn-primary" onClick="javascript: window.location.href='./dashboard/candidate/apply/${payload.id}'">Apply Now</button>
                    `:`
                        <button class="btn btn-primary" onClick="javascript: localStorage.setItem('currentUrl','./dashboard/candidate/apply/${payload.id}'); window.location.href='./dashboard'">Login to apply now</button>
                    `;
                    var content= `
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                    <h5 class="card-title">${payload.job_title}</h5></div>
                                    <div class="col-md-3">
                                        ${isLogin}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1">
                                        <img src="assets/images/job4.png" width="30px" alt=""  class="img-responsive" />
                                    </div>
                                    <div class="col-md-8">
                                        <p class="card-text"><small><span class="fa fa-location"></span>${payload.location}</small></p>
                                        <ul class="list-group list-group-horizontal">
                                            <li class="list-group-item list-group-item-dark" ><small>${payload.job_type}</small></li>
                                            <li class="list-group-item list-group-item-dark"><small>${payload.job_site}</small></li>
                                            <li class="list-group-item list-group-item-dark"><small>${payload.job_duration}</small></li>
                                        </ul>
                                    </div>
                                </div>
                                <p><strong>Company Introduction</strong></p>

                                <p class="card-text">${payload.about_company}</p>
                                <p><strong>Job Description</strong></p>

                                <p class="card-text">${payload.job_desc}</p>

                                <p><strong>Job Requirements</strong></p>
                                <p class="card-text">
                                    ${payload.job_requirement}
                                </p>
                                <p><strong>Skills and Qualifications</strong></p>

                                <p>
                                ${payload.skills}
                                </p>
                            </div>
                        </div>
                    `;
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
            
            $( '.form-apply' )
                .submit( function( e ) {
                    const formContent=$('.modal-body').children().clone();

                    $.ajax({
                        url: $('.form-apply').attr('action'),
                        type: 'POST',
                        data: new FormData( this ),
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            $('.modal-body').html(`
                                <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                    <img src="assets/loading.gif" style="width: 20%;" />
                                </div>
                            `);
                            $('.btn-submit-application').addClass('disabled');
                        },
                        complete: function(){
                            $('.btn-submit-application').removeClass('disabled');
                        },
                        success: function(data){
                            if(data.status){
                                $('.modal-body').html(
                                    `
                                        <div class="alert alert-success">${data.message}</div>
                                    `
                                );

                            }else{
                                $('.modal-body').html(
                                    `
                                        <div class="alert alert-danger">${data.message}</div>
                                    `
                                );
                                $('.btn-submit-application').removeClass('disabled');
                            }
                        },
                        error: function(error){
                            console.log(error)
                            $('.modal-body').empty()
                            $('.modal-body').html(formContent)
                            $('.btn-submit-application').removeClass('disabled');
                        },
                    });
                    e.preventDefault();
                } );
        })
            
    </script>


    <!-- footer17 -->
<?php include ('footer.php'); ?>