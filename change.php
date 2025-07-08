<?php 
$title="Password reset";
include ('header.php'); ?>
    
    <!-- w3l-forms-31 -->
    <section class="w3l-forms-31">
        <div id="w3l-forms-31_sur">
            <div class="wrapper">
                <div class="d-grid">
                    <div class="w3l-forms-31-top">
                        <h4>Password reset</h4>
                        <p>You have forgotten your password? No worries. You can always reset it here.</p>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="assets/lock.png" width="70%" />
                        </div>
                        
                    </div>
                    <div class="w3l-forms-31-right" style="padding-top:30%;">
                        <h4>Change passowrd</h4>
                        <form action="dashboard/candidate/change-password" method="post" class="login-form">
                            <input type="hidden" name="user_id" value="" class="user_id" />
                            <div class="form-input">
                                <input type="password" name="password" placeholder="Enter your new password" required="" />
                            </div>
                            <div class="form-input">
                                <input type="password" name="cpassword" placeholder="Confirm your password" required="" />
                            </div>
                            <button class="btn btn-process" type="submit">Change password </button>
                            <div class="clear"></div>
                            <p class="form_acunt text-center"><a href="login.php">Back to login</a></p>
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
            var user=atob(localStorage.getItem("token"));
            user=user?JSON.parse(user):{};
            //console.log(user);
            $('.user_id').val(user.id);
            $( '.login-form' ).submit( function( e ) {
                $.ajax({
                    url: $('.login-form').attr('action'),
                    type: 'POST',
                    data: new FormData( this ),
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('.btn-process').removeClass('disabled');
                        $(".btn-process").html("Processing...");
                    },
                    complete: function(){
                        $('.btn-process').removeClass('disabled');
                        $(".btn-process").html("Change password");
                    },
                    success: function(data){
                        if(data.status){
                            localStorage.setItem("token", btoa(JSON.stringify(data.payload)));
                            localStorage.setItem("isLogin", true);
                            var currentUrl=localStorage.getItem('currentUrl');
                            window.location.href="./login.php"
                            alert("Password changed successfully. Kindly proceed to login now.")

                        }else{
                            alert(data.message)
                        }
                    },
                    error: function(error){
                        console.log(error)
                    },
                });
                e.preventDefault();
            } );
        });
    </script>
<?php include ('footer.php'); ?>
