
<?php include ('header.php'); ?>
    
    <!-- w3l-forms-31 -->
    <section class="w3l-forms-31">
        <div id="w3l-forms-31_sur">
            <div class="wrapper">
                <div class="d-grid">
                    <div class="w3l-forms-31-top">
                        <h4>Access Login</h4>
                        <p>Your account is secured. You will have to supply your login credentials to access your account.</p>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="assets/lock.png" width="70%" />
                        </div>
                        
                    </div>
                    <div class="w3l-forms-31-right">
                        <h4>Employer Login Page</h4>
                        <form action="dashboard/candidate/login" method="post" class="login-form">
                            <div class="form-input">
                                <input type="email" name="email" placeholder="Enter your email" required="" />
                            </div>
                            <div class="form-input">
                                <input type="password" name="password" placeholder="Enter your password" required="" />
                            </div>
                            <a href="reset.php" class="forgot">Forgot Password?</a>
                            <button class="btn btn-process" type="submit">Login</button>
                            <div class="clear"></div>
                            <p class="form_acunt text-center">Don't have an account? <a href="signup.php">Signup Now</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- w3l-forms-31 -->

    <!-- footer17 -->


    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            
            $( '.login-form' ).submit( function( e ) {
                $('.btn-process').html("Processing...");
                var form = $(this).closest('form');
                
                var url=$(form).attr('action');
                
                var values=$(form).serializeArray();
                // alert(url)
                $.ajax({
                    url: $('.login-form').attr('action'),
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
                            localStorage.setItem("token", btoa(JSON.stringify(data.payload)));
                            localStorage.setItem("isLogin", true);
                            if(data.payload.isDefault=='YES'){
                                localStorage.removeItem("isLogin");
                                setTimeout(()=>{
                                    alert("Kindly change your password now.")
                                    window.location.href="./change.php"
                                }, 500);
                            }else{
                                setTimeout(()=>{
                                    var currentUrl=localStorage.getItem('currentUrl');
                                    if(currentUrl){
                                        localStorage.removeItem('currentUrl')
                                        window.location.href=currentUrl
                                    }else{
                                        window.location.href="./dashboard"
                                    } 
                                }, 500);
                            }
                        }else{
                            // $('.btn-process').html('Login');
                            alert(data.message)
                        }
                    },
                    error: function(error){
                        console.log(error)
                        // $('.btn-process').removeClass('disabled');
                        // $('.btn-process').html('Login');
                    },
                });
                e.preventDefault();
            })
        });
    </script>
<?php include ('footer.php'); ?>
