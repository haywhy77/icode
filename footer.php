    <footer class="w3l-footer">
        <div class="w3l-footer-16 py-5">
            <div class="container py-md-5">
                <div class="row footer-p">
                    <div class="col-lg-6 col-md-6 pe-lg-5">
                        <h2 class="footerw3l-logo"><a class="navbar-brand" href="index.html">
                               <img src="assets/images/icodefooter.png" alt="FinAgenc Logo" class="img-fluid logo-footer" width="150" height="50">
                            </a></h2>

                    </div>
                    <div class="col-lg-6 col-md-6">
                        <h4 class="mt-3">Subscribe With Us</h4>
                    </div>
                </div>
                <div class="row footer-p mt-5 pt-lg-4">
                    <div class="col-lg-4 col-md-6 pe-lg-5">
                        <div class="column">
                            <h3>Phone</h3>
                            <p><a href="tel:+2347036314495">+234 703 631 4495</a></p>
                        </div>

                        <div class="column mt-lg-5 mt-4">
                            <h3>Address </h3>
                            <p>Flat 2, Behind Dominion Church, PW Kubwa, Abuja, Nigeria.</p>
                        </div>

                    </div>
                    <div class="col-lg-4 col-md-6">

                        <div class="column">
                            <h3>Support</h3>
                            <p><a href="mailto:info@icoderesources.com.ng" class="mail"><span class="__cf_email__">info@icoderesources.com.ng</span></a></p>
                        </div>
                        <div class="column mt-lg-5 mt-4">
                            <h3>Follow</h3>
                            <ul class="social">
                                <li><a href="https://www.facebook.com/icoderesources" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li><a href="https://twitter.com/imoleayomic" target="_blank"><i class="fab fa-twitter"></i></a>
                                </li>
                                <li><a href="https://www.instagram.com/icoderesources" target="_blank"><i class="fab fa-instagram"></i></a>
                                </li>
                                <li><a href="#linkedin"><i class="fab fa-linkedin-in"></i></a></li>

                            </ul>
                        </div>

                    </div>
                    <div class="col-lg-4 col-md-6 mt-lg-0 mt-4 pl-xl-0">
                        <h3>Newsletter</h3>
                        <div class="end-column">
                            <form action="#" class="subscribe d-flex" method="post">
                                <input type="email" name="email" placeholder="Email Address" required="">
                                <button class="btn btn-secondary"><i class="fas fa-paper-plane"></i></button>
                            </form>
                            <p class="mt-4">Subscribe to our mailing list and get updates to your email inbox.</p>
                        </div>
                    </div>
                </div>

                <div class="below-section pt-lg-4 mt-5">
                    <div class="row">

                        <p class="copy-text col-lg-7">&copy; <?php echo date("Y"); ?> iCode Resources. All rights reserved.</a>
                        </p>
                        <div class="col-lg-5 w3footer-gd-links d-flex">

                            <a href="#privacy">Privacy Policy</a>
                            <a href="#terms" class="mx-3">Terms of service</a>
                            <a href="#faq">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
        <!--/Js-scripts-->
    <!-- move top -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
        <span class="fas fa-arrow-up" aria-hidden="true"></span>
    </button>
    <script data-cfasync="false" src="../../../../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
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
    <!-- //move-top-->
    <!-- Template JavaScript -->
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/theme-change.js"></script>
    <!--/owlcarousel-->
    <script src="assets/js/owl.carousel.js"></script>
    <!-- script for banner slider-->
    <script>
        $(document).ready(function() {
            $('.owl-one').owlCarousel({
                loop: true,
                margin: 0,
                nav: false,
                responsiveClass: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplaySpeed: 1000,
                autoplayHoverPause: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    480: {
                        items: 1
                    },
                    667: {
                        items: 1
                    },
                    1000: {
                        items: 1
                    }
                }
            })
        })

    </script>
    <!-- //script -->
    <!-- //tesimonials-->
    <script>
        $(document).ready(function() {
            $("#owl-demo1").owlCarousel({
                loop: true,
                nav: false,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    736: {
                        items: 1,
                        nav: false
                    }
                }
            })
        })

    </script>
    <!-- //tesimonials-->
    <!-- video popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.popup-with-zoom-anim').magnificPopup({
                type: 'inline',

                fixedContentPos: false,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: false,

                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            });

            $('.popup-with-move-anim').magnificPopup({
                type: 'inline',

                fixedContentPos: false,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: false,

                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-slide-bottom'
            });
        });

    </script>
    <!-- //video popup -->
    <!-- MENU-JS -->
    <script>
        $(window).on("scroll", function() {
            var scroll = $(window).scrollTop();

            if (scroll >= 80) {
                $("#site-header").addClass("nav-fixed");
            } else {
                $("#site-header").removeClass("nav-fixed");
            }
        });

        //Main navigation Active Class Add Remove
        $(".navbar-toggler").on("click", function() {
            $("header").toggleClass("active");
        });
        $(document).on("ready", function() {
            if ($(window).width() > 991) {
                $("header").removeClass("active");
            }
            $(window).on("resize", function() {
                if ($(window).width() > 991) {
                    $("header").removeClass("active");
                }
            });
        });

    </script>
    <!-- //MENU-JS -->

    <!-- disable body scroll which navbar is in active -->
    <script>
        $(function() {
            $('.navbar-toggler').click(function() {
                $('body').toggleClass('noscroll');
            })
        });

    </script>
    <!-- //disable body scroll which navbar is in active -->

    <!-- //bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>

</body>
</html>