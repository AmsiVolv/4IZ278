<?php
$isAdmin = false;
$pageTitle = 'Admin panel';
require_once './inc/user.php';


#kontrolujeme pokud je adminem
if(!$isAdmin){
    header('Location: index.php');
}
#konec kontroly admina

include './inc/header.php';

?>

<html>
<body>

<div class="section-heading">
    <h2>Personal admin area</h2>
    <div class="line"></div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center mt-">
        <div class="col-md-10 card shadow-lg o-hidden border-0">
            <div class="row">
                <div class="col-md-9">
                    <div class="d-flex bd-highlight align-self-baseline">
                        <div class="p-1 w-100 bd-highlight">
                            <h3 class="text-center">
                                List of your actions
                            </h3>
                        </div>
                    </div>
                    <hr>
                    <?php

                    #pokud clovek ted udelal rezervaci, tak oznamime ho
                    if(isset($_SESSION['success'])) {
                        echo '
                    <div class="alert alert-dismissable alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            ×
                        </button>
                        <h4 class="text-center">
                            Congratulation!
                        </h4> ' . htmlspecialchars($_SESSION['success']) . '
                    </div>';
                        unset($_SESSION['success']);
                    }

                    #konec bloku

                    #pokud byly nejaky chyby u editace
                    if (isset($_SESSION['errors'])) {
                        echo '
                                       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                                    ×
                                       </button>
                                       <div class="alert alert-dismissable alert-warning">
                                       <h4 class="text-center">
                                             We found this mistakes!
                                        </h4>';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<p class="text-danger">' . $error . '</p>';
                        }
                        unset($_SESSION['errors']);
                        echo '</div>';
                    }
                    #konec bloku
                    ?>



                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <button class="btn dento-btn booking-btn registr mt-5 mb-5">
                                <a href="pricing.php">
                                    Edit/Add a service
                                </a>
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <button class="btn dento-btn booking-btn registr mt-5 mb-5">
                                <a href="editUser.php">
                                    Edit users
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-md-3">
                    <h3 class="text-center p-3">
                        Chat-Admin
                        <hr>
                    </h3>
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'inc/footer.php';
?>
</body>
</html>