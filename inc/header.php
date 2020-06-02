<?php
require_once './inc/user.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Dento - Dentist &amp; Medical HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title><?php echo (!empty($pageTitle)?$pageTitle.' - ':'')?>Dento</title>

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
                <div class="col-6 col-md-3 col-lg-4 text-right">
                    <div>      <?php
                        if (!empty($_SESSION['user_id'])){
                            echo '<strong>'.htmlspecialchars($_SESSION['user_name']).'</strong>';
                            echo ' - ';
                            echo '<a href="logout">Log out</a>';
                        }else{
                            echo '<a href="login">Log in</a>';
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
                    <a class="nav-brand" href="./index"><img src="./img/core-img/logo.png" alt=""></a>

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
                                <li><a href="./index">Home</a></li>
                                <li><a href="./about">About</a></li>
                                <li><a href="./service">Service</a></li>
                                <li><a href="./pricing">Pricing</a></li>
                                <li><a href="./blog">Blog</a></li>
                                <li><a href="./contact">Contact</a></li>
                            </ul>
                        </div>
                        <!-- Nav End -->
                    </div>

                    <!-- Booking Now Button -->
                    <?php
                    if(isset($_SESSION['user_id'])){
                        echo '<a href="./personal" class="btn dento-btn booking-btn">Personal area</a>';
                    }else{
                        echo '<a href="./signup" class="btn dento-btn booking-btn">Registr now</a>';
                    }
                    ?>
                    <a href="./cal-reservation" class="btn dento-btn booking-btn registr">Booking Now</a>
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