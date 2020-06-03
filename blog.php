<?php
$isAdmin = false;
$pageTitle = 'Blog page';
require_once './inc/clasees/PostsMain.php';


if(!$isAdmin){
    $isAdmin = false;
}

include './inc/header.php';
?>

  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-gradient-overlay" style="background-image: url(img/bg-img/12.jpg);">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title">Blog</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcumb--con">
    <div class="container">
      <div class="row">
        <div class="col-9">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
          </nav>
        </div>
          <?php
          if($isAdmin){
              echo '         
          <div class="col-3 breadcrumb">
              <button class="btn dento-btn booking-btn">
                  <a href="postCreate.php" class="">New post</a>
              </button>
          </div>';
          }
          ?>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->

  <!-- *****Blog Area Start ***** -->
  <section class="dento-blog-area mt-50">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-8">

          <!-- Single Blog Item -->
          <div class="single-blog-item style-2 d-flex flex-wrap align-items-center mb-50">
            <!-- Blog Thumbnail -->
            <div class="blog-thumbnail">
              <a href="blog-details.php">
                <img src="./img/bg-img/16.jpg" alt="">
              </a>
            </div>
            <!-- Blog Content -->
            <div class="blog-content">
              <a href="blog-details.php" class="post-title">How your mouth bacteria can harm your lungs</a>
              <p>Donec tempor, lorem et euismod eleifend, est lectus laoreet ante, sed accusan justo diam nec tincidunt interdum ante consectetur dapibus molestie ...</p>
              <div class="post-meta">
                <a href="#"><i class="icon_clock_alt"></i> 28 Sep 2018</a>
              </div>
            </div>
          </div>


          <!-- Single Blog Item -->
            <?php
            $a = new Post();
            $a->test($a->getPosts());
            ?>


          <!-- Pagination -->
          <nav class="dento-pagination mb-100">
            <ul class="pagination">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#"><i class="fa fa-angle-right"></i></a></li>
            </ul>
          </nav>
        </div>

        <!-- Dento Sidebar Area -->
        <div class="col-12 col-lg-4">
          <div class="dento-sidebar">

            <!-- Single Widget Area -->
            <div class="single-widget-area search-widget">
              <form action="#" method="post">
                <input type="search" name="search" class="form-control" placeholder="Search ...">
                <button type="submit"><i class="icon_search"></i></button>
              </form>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area catagories-widget">
              <h5 class="widget-title">Categories</h5>

              <!-- catagories list -->
              <ul class="catagories-list">
                <li><a href="#">Dentistry Articles</a></li>
                <li><a href="#">Oral Health</a></li>
                <li><a href="#">Dental Technology</a></li>
                <li><a href="#">Kids Care Dental</a></li>
                <li><a href="#">Healthy Smiles, Inside &amp; Out</a></li>
              </ul>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area news-widget">
              <h5 class="widget-title">Recent News</h5>

              <!-- Single News Area -->
              <div class="single-news-area d-flex align-items-center">
                <div class="blog-thumbnail">
                  <img src="./img/bg-img/21.jpg" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Tooth decay: why good dental hygiene is important</a>
                  <span class="post-date">28 Sep 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex align-items-center">
                <div class="blog-thumbnail">
                  <img src="./img/bg-img/22.jpg" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Six reasons your breath might smell like poop</a>
                  <span class="post-date">28 Sep 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex align-items-center">
                <div class="blog-thumbnail">
                  <img src="./img/bg-img/23.jpg" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Everything you need to know about sinus infection</a>
                  <span class="post-date">28 Sep 2018</span>
                </div>
              </div>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area adds-widget">
              <a href="#"><img class="w-100" src="./img/bg-img/adds.png" alt=""></a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- *****Blog Area End ***** -->

  <?php
  include 'inc/footer.php';
  ?>

</body>

</html>