<?php
$isAdmin = false;
$pageTitle = 'Pricing page';
include './inc/header.php';

if (!empty($_REQUEST['status'])) {
    alert('ID must be a number');
}
if (!empty($_REQUEST['range'])) {
    alert('ID out of range');
}
#fce na zobrazeni chyb
function alert($msg)
{
    echo "<script type='text/javascript'>alert('$msg');</script>";
}
#konec regionu

$servicesQuery=$db->prepare('SELECT * FROM services_sem');
$servicesQuery->execute();
$services=$servicesQuery->fetchAll();
?>
  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-gradient-overlay" style="background-image: url(img/bg-img/12.jpg);">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title">Our Pricing</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcumb--con">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Our Pricing</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->

  <!-- ***** Dento Pricing Table Area Start ***** -->
  <section class="dento-pricing-table-area mt-50 section-padding-0-100">
    <div class="container">
      <div class="row">
        <div class="col-12">
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

        <div class="col-12">
          <div class="more-btn text-center mt-50">
            <a href="#" class="btn dento-btn booking-btn mr-50">Read More <i class="fa fa-angle-double-right"></i></a>
              <?php if($isAdmin){echo '<a href="editservice.php" class="btn dento-btn booking-btn registr ml-50">New service <i class="fa fa-angle-double-right"></i></a>';}?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Dento Pricing Table Area End ***** -->

  <?php
  include 'inc/footer.php';
  ?>
</body>

</html>