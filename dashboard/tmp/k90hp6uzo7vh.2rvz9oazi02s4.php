<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login &mdash; <?= ($business) ?></title>

        <!-- META SECTION -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <!-- END META SECTION -->
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" href="<?= ($BASE) ?>/<?= ($ASSETS) ?>css/styles.css?v=1.0.3">
        <!-- EOF CSS INCLUDE -->
    </head>
    <body>
        <!-- PAGE WRAPPER -->
        <div class="page">

            <!-- PAGE CONTENT WRAPPER -->
            <div class="page__content" id="page-content">

                <!-- PAGE LOGIN CONTAINER -->
                <div class="important-container login-container">
                    
                    <div class="content">

                        
                        <br><br><p><br><br>
                        <p class="caption text-center margin-bottom-30">Welcome to Deen Consult Services</p>
                        
                        <?php foreach ((\Flash::instance()->getMessages()?:[]) as $msg): ?>
                            <div class="alert alert-<?= ($msg['status']) ?> alert-dismissable">
                                <?= ($msg['text'])."
" ?>
                            </div>
                        <?php endforeach; ?>
                        <form action="<?= ($BASE) ?>/<?= ($form['action']) ?>" method="post" autocomplete="off">
                            <input type="hidden" name="session_csrf" value="<?= ($CSRF) ?>" />
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email or phone">
                            </div>
                            <div class="form-group margin-bottom-20">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Your password" autocomplete="new-password">
                            </div>

                            <div class="form-group margin-bottom-30">
                                <div class="row">
                                    <div class="col-6">
                                       <div class="form-check">
                                          <input class="form-check-input" type="checkbox" value="">
                                          <label class="form-check-label">Remember Me</label>
                                       </div>                                        
                                    </div>
                                    <div class="col-6 text-end">
                                        <a href="<?= ($BASE) ?>/<?= ($form['action']) ?>/password-forget">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group margin-bottom-30">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8">
                                        <button class="btn btn-primary btn-block">Login Account</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="divider"></div>

                        <div class="form-group text-center">
                            <div class="row">
                                <div class="col-4">
                                    <a href="../index.php" class="text-muted">Home</a>
                                </div>
                                <div class="col-4">
                                    <a href="../about.php" class="text-muted">About Us</a>
                                </div>
                                <div class="col-4">
                                    <a href="../contact.php" class="text-muted">Contacts</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- PAGE LOGIN CONTAINER -->

                <!-- PAGE CONTENT CONTAINER -->
                <div class="content d-none d-lg-block" id="content" style="background: url(<?= ($BASE) ?>/<?= ($ASSETS) ?>assets/img/backgrounds/bridge.jpg) left center no-repeat; width: 100vw; height: 100vh; background-size: cover;">
                    <a href="/" class="logo-holder logo-holder--lg logo-holder--wide" style="margin-top: 26%;">
                        <div class="logo-text">
                            <strong class="text-primary">
                                <img src="<?= ($BASE) ?>/ui/img/logo.png" style="width:250px;" />
                            </strong>
                        </div>
                        
                    </a>
                    <div style="margin-top:50%; margin-left:20%; color: #fff; font-size: 5em;"><?= ($business) ?></div>
                </div>
                <!-- //END PAGE CONTENT CONTAINER -->

            </div>
            <!-- //END PAGE CONTENT -->

        </div>
        <!-- //END PAGE WRAPPER -->

        
        <!-- //END TEMPLATE SETTINGS -->
        <!-- IMPORTANT SCRIPTS -->
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/jquery/jquery-migrate.min.js"></script>
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/bootstrap/bootstrap.bundle.min.js"></script>
        
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
        <!-- END IMPORTANT SCRIPTS -->
        <!-- TEMPLATE SCRIPTS -->
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/app.js"></script>
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/plugins.js"></script>
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/demo.js"></script>
        <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($ASSETS) ?>js/settings.js"></script>
        <!-- END TEMPLATE SCRIPTS -->
    </body>
</html>