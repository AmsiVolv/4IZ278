<?php
require_once 'user.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Dento - Dentist &amp; Medical HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Dento - Dentist &amp; Medical HTML Template</title>

    <!-- Favicon -->
    <link rel="icon" href="./img/core-img/favicon.ico">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="style.css">

</head>

<body>
<!-- Preloader -->
<div id="preloader">
    <div class="preload-content">
        <div id="dento-load"></div>
    </div>
</div>

<!-- ***** Header Area Start ***** -->
<header class="header-area">
    <!-- Top Header Area -->
    <div class="top-header-area">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <!-- Top Content -->
                <div class="col-6 col-md-9 col-lg-8">
                    <div class="top-header-content">
                        <a href="#" data-toggle="tooltip" data-placement="bottom" title="28 Jackson Street, Chicago, 7788569 USA"><i class="fa fa-map-marker"></i> <span>28 Jackson Street, Chicago, 7788569 USA</span></a>
                        <a href="#" data-toggle="tooltip" data-placement="bottom" title="info.dento@gmail.com"><i class="fa fa-envelope"></i> <span>info.dento@gmail.com</span></a>
                    </div>
                </div>
                <!-- Top Header Social Info -->
                <div class="col-6 col-md-3 col-lg-4">
                    <div>      <?php
                        if (!empty($_SESSION['user_id'])){
                            echo '<strong>'.htmlspecialchars($_SESSION['user_name']).'</strong>';
                            echo ' - ';
                            echo '<a href="logout.php">odhlásit se</a>';
                        }else{
                            echo '<a href="login.php">přihlásit se</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Header End -->

    <!-- Main Header Start -->
    <div class="main-header-area">
        <div class="classy-nav-container breakpoint-off">
            <div class="container">
                <!-- Classy Menu -->
                <nav class="classy-navbar justify-content-between" id="dentoNav">

                    <!-- Logo -->
                    <a class="nav-brand" href="../index.php"><img src="./img/core-img/logo.png" alt=""></a>

                    <!-- Navbar Toggler -->
                    <div class="classy-navbar-toggler">
                        <span class="navbarToggler"><span></span><span></span><span></span></span>
                    </div>

                    <!-- Menu -->
                    <div class="classy-menu">

                        <!-- Close Button -->
                        <div class="classycloseIcon">
                            <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                        </div>

                        <!-- Nav Start -->
                        <div class="classynav">
                            <ul id="nav">
                                <li><a href="../index.php">Home</a></li>
                                <li><a href="#">Pages</a>
                                    <ul class="dropdown">
                                        <li><a href="../index.php">- Home</a></li>
                                        <li><a href="about.html">- About Us</a></li>
                                        <li><a href="service.html">- Service</a></li>
                                        <li><a href="pricing.html">- Pricing</a></li>
                                        <li><a href="blog.html">- Blog</a></li>
                                        <li><a href="blog-details.html">- Blog Details</a></li>
                                        <li><a href="contact.html">- Contact</a></li>
                                        <li><a href="#">- Dropdown</a>
                                            <ul class="dropdown">
                                                <li><a href="#">- Dropdown Item</a></li>
                                                <li><a href="#">- Dropdown Item</a>
                                                    <ul class="dropdown">
                                                        <li><a href="#">- Even Dropdown</a></li>
                                                        <li><a href="#">- Even Dropdown</a></li>
                                                        <li><a href="#">- Even Dropdown</a></li>
                                                        <li><a href="#">- Even Dropdown</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href="#">- Dropdown Item</a></li>
                                                <li><a href="#">- Dropdown Item</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="about.html">About</a></li>
                                <li><a href="service.html">Service</a></li>
                                <li><a href="pricing.html">Pricing</a></li>
                                <li><a href="#">Blog</a>
                                    <ul class="dropdown">
                                        <li><a href="blog.html">- Blog</a></li>
                                        <li><a href="blog-details.html">- Blog Details</a></li>
                                    </ul>
                                </li>
                                <li><a href="./contact.html">Contact</a></li>
                            </ul>
                        </div>
                        <!-- Nav End -->
                    </div>

                    <!-- Booking Now Button -->
                    <a href="#" class="btn dento-btn booking-btn">Booking Now</a>
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->

<!-- ******* All JS ******* -->
<!-- jQuery js -->
<script src="js/jquery.min.js"></script>
<!-- Popper js -->
<script src="js/popper.min.js"></script>
<!-- Bootstrap js -->
<script src="js/bootstrap.min.js"></script>
<!-- All js -->
<script src="js/dento.bundle.js"></script>
<!-- Active js -->
<script src="js/default-assets/active.js"></script>

</body>

</html>