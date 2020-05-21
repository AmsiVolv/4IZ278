<?php
//načteme připojení k databázi a inicializujeme session
$pageTitle = 'Login page';
require_once 'inc/user.php';
require_once 'oauth.php';

if (!empty($_SESSION['user_id'])){
    //uživatel už je přihlášený, nemá smysl, aby se registroval
    header('Location: index.php');
    exit();
}

$errors=false;
if (!empty($_POST)){
    #region zpracování formuláře
    $userQuery=$db->prepare('SELECT * FROM users_sem WHERE email=:email LIMIT 1;');
    $userQuery->execute([
        ':email'=>trim($_POST['email'])
    ]);
    if ($user=$userQuery->fetch(PDO::FETCH_ASSOC)){

        if (password_verify($_POST['password'],$user['password'])){
            //heslo je platné => přihlásíme uživatele
            $_SESSION['user_id']=$user['id_user'];
            $_SESSION['user_name']=$user['name'];
            header('Location: index.php');
            exit();
        }else{
            $errors=true;
        }
    }else{
        $errors=true;
    }
    #endregion zpracování formuláře
}

include 'inc/header.php';
?>
<div class="col-12 col-md-12">
    <!-- Form -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-6">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h4 class="text-dark mb-4">Welcome Back!</h4>
                                    </div>
                                    <form action="login.php" method="post">
                                        <?php
                                        echo ($errors?
                                            '<div class="row justify-content-md-center text-center">
                                                <div class="col-lg-12">
                                                    <p class="text-danger">Wrong email and password combination</p>
                                                </div>
                                         </div>':'');
                                        ?>
                                        <div class="form-group"><input type="email" name="email" id="email" class="form-control mb-30 <?php echo ($errors?'is-invalid':''); ?>" placeholder="Your Email" required value="<?php echo htmlspecialchars(@$_POST['email']);?>"/></div>
                                        <div class="form-group"><input type="password" name="password" id="password" class="form-control mb-30 <?php echo ($errors?'is-invalid':''); ?>" placeholder="Password" required value=""/></div>
                                        <button class="btn btn-primary btn-block text-white btn-user" type="submit">Login</button>
                                        <hr />
                                        <a href="<?php echo $auth_url?>" class="btn btn-primary btn-block text-white btn-google btn-user" role="button"><i class="fa fa-google"></i>  Login with Google</>
                                        <a class="btn btn-primary btn-block text-white btn-facebook btn-user" role="button"><i class="fa fa-facebook"></i>  Login with Facebook</a>
                                        <hr
                                        />
                                    </form>
                                    <div class="text-center"><a class="small" href="forgot-password.html">Forgot Password?</a></div>
                                    <div class="text-center"><a class="small" href="register.html">Create an Account!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form end -->
</div>

