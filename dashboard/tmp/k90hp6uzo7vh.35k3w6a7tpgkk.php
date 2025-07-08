<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>DeenConsult | login </title>
	<link rel="stylesheet" href="<?= ($BASE) ?>/<?= ($ASSETS) ?>css/styles.css">
	<link rel="stylesheet" href="../assets/css/style-freedom.css">
    <link rel="icon" href="../assets/images/logo2.png">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <section class="w3l-forms-31">
        <div id="w3l-forms-31_sur">
            <div class="wrapper">
				<?php foreach ((\Flash::instance()->getMessages()?:[]) as $msg): ?>
					<div class="alert alert-<?= ($msg['status']) ?> alert-dismissable">
						<?= ($msg['text'])."
" ?>
					</div>
				<?php endforeach; ?>
                <div class="d-grid">
                    <div class="w3l-forms-31-top">
                        <h4>Access Login</h4>
                        <p>Your account is secured. You will have to supply your login credentials to access your account.</p>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="../assets/lock.png" width="70%" />
                        </div>
                        
                    </div>
                    <div class="w3l-forms-31-right">
                        <h4>Applicant Login Page</h4>
                        <form action="<?= ($BASE) ?>/" method="post" class="login-form">
							<input type="hidden" name="session_csrf" value="<?= ($CSRF) ?>" />
                            <div class="form-input">
                                <input type="email" name="email" placeholder="Enter your email" required="" />
                            </div>
                            <div class="form-input">
                                <input type="password" name="password" placeholder="Enter your password" required="" />
                            </div>
                            <a href="<?= ($BASE) ?>/reset" class="forgot">Forgot Password?</a>
                            <button class="btn btn-process" type="submit">Login</button>
                            <div class="clear"></div>
                            <p class="form_acunt text-center">Don't have an account? <a href="<?= ($BASE) ?>/signup">Signup Now</a></p>
							<p class="form_acunt text-center"> <a href="../">Back to home page</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

	<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/bootstrap/bootstrap.bundle.min.js"></script>

	<script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
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

</body>

</html>