<?php echo $this->render('includes/header.html',NULL,get_defined_vars(),0); ?>
<!-- PAGE CONTENT WRAPPER -->
<div class="page__content" id="page-content">
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <?php echo $this->render('includes/pageHeader.htm',NULL,get_defined_vars(),0); ?>
        <div class="container-fluid"></div>
            <?php echo $this->render($template,NULL,get_defined_vars(),0); ?>

            <div class="card margin-bottom-0">
                <div class="card-body text-muted">
                    &copy; All Rights Reserved. <?= ($business) ?> - <?= (date('Y')) ?> | Powered by <a href="https://icoderesources.com.ng" target="_blank">iCode Resources</a>
                </div>
            </div>
    </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
</div>

<?php echo $this->render('includes/footer.html',NULL,get_defined_vars(),0); ?>