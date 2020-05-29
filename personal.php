<?php
$isAdmin = false;
$pageTitle = 'Reservation';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if ($isAdmin) {
    $isAdmin=true;
}
#konec kontroly admina

include './inc/header.php';

$servicesQuery=$db->prepare('SELECT * FROM services_sem');
$servicesQuery->execute();
$services=$servicesQuery->fetchAll();

?>
<html>

<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-12 col-xl-12">
            <div class="card shadow-lg o-hidden border-0 my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-4 col-xl-4 ml-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h4 class="text-dark mb-4">Welcome Back!</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-xl-8 d-none d-lg-flex p-5">
                                <div class="dento-price-table table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                        <tr>
                                            <th scope="col">Service Names</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Price</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($services as $service){
                                            echo '
                <tr>
                  <th scope="row">'.htmlspecialchars(@$service['name']).'</th>
                  <td>'.htmlspecialchars(@$service['description']).'</td>
                  <td>$'.htmlspecialchars($service['price']).'</td> ';

                                            if($isAdmin){
                                                echo '<td class="text-center">
                                  <a class="text-success pr-2" href="editservice.php?id='.$service['id_ser'].'">Edit</a>
                                  <a class="text-danger pl-2" href="editservice.php?remove='.$service['id_ser'].'">Remove</a>
                              </td>';
                                            }
                                            echo '</tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>

<?php
include 'inc/footer.php';
?>
</body>
</html>
