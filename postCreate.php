<?php
$isAdmin = false;
$pageTitle = 'Create a post page';
require_once './inc/clasees/Post.php';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if(!$isAdmin){
    $isAdmin = false;
}
if(!isset($_SESSION['user_id']) or !$isAdmin){
    header('Location: login.php');
}

$errors = [];


if(!empty($_POST)){
    if(checkCSRF($_SERVER['PHP_SELF'], $_POST['csrf'])) {
        $text = new Post();
        $text->insertInto($_POST['postText'], $_POST['postTitle'], $_FILES['postIMG']);
        var_dump($_FILES);
    }else{
        $errors['csrf']='Invalid CSRF token';
    }
}

if(!empty($_GET['id']) and $isAdmin){
    if(is_numeric($_GET['id'])){
        $postId = htmlspecialchars($_GET['id']);
        $postMain = new PostsMain();
        if($postMain->checkPost($postId)){
            $postMain->deletePost($postId);
            header('Location: blog.php');
        }else{
            $errors['id']='Post ID out of range.';
        }
    }else{
        $errors['id']='Post ID must be a number.';
    }
}

include './inc/header.php';
?>
<div class="section-heading">
    <h2>Edit reservation</h2>
    <div class="line"></div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6 card shadow-lg o-hidden border-0 p-5">
                            <?php
                            if(!empty($errors)){
                                echo '
                                    <div class="row">
                                        <div class="col-md-12">';
                                            foreach ($errors as $error){
                                                echo '<p class="text-center text-danger">'.$error.'</p>';
                                            }
                                        echo '</div>
                                    </div>';
                            }
                            ?>
                            <form name="postCreateForm" role="form" method="post" action="postCreate.php" enctype="multipart/form-data">
                                <input type="hidden" name="csrf" value="<?php echo (getCSRF($_SERVER['PHP_SELF'])); ?>">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <label for="reservationDate">
                                                Post title:
                                            </label>
                                            <input type="text"  class="form-control" id="postTitle" name="postTitle" value="<?php htmlspecialchars(@$_POST['postTitle']) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <label for="reservationDate">
                                                Post text:
                                            </label>
                                            <textarea required class="form-control" id="postText" name="postText" value="<?php htmlspecialchars(@$_POST['postText']) ?>"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 text-center mb-2">
                                            <label for="reservationDate">
                                                Chose a file:
                                            </label>
                                            <input type="file" required class="form-control-file" id="postIMG" name="postIMG">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="submit" name="submit">
                                        Submit
                                    </button>
                                    <button type="cancel" class="btn btn-secondary">
                                        <a class="text-decoration-none text-white" href="blog.php">Cancel</a>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
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
