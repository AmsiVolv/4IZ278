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
    <!-- Section Heading -->
    <div class="section-heading">
        <h2>Login now</h2>
        <div class="line"></div>
    </div>
    <!-- Form -->
    <form action="login.php" method="post">
        <?php
            echo ($errors?'
        <div class="row justify-content-md-center text-center">
            <div class="col-lg-4">
                <p>Wrong email and password combination</p>
            </div>
        </div>
            ':'');
        ?>
        <div class="row justify-content-md-center">
            <div class="col-lg-4">
                <input type="email" name="email" id="email" class="form-control mb-30 <?php echo ($errors?'is-invalid':''); ?>" placeholder="Your Email" required value="<?php echo htmlspecialchars(@$_POST['email']);?>">
            </div>
        </div>
        <div class="row justify-content-md-center">
            <div class="col-lg-4">
                <input type="password" name="password" id="password" class="form-control mb-30 <?php echo ($errors?'is-invalid':''); ?>" placeholder="Password" required value="">
            </div>
        </div>
        <div class="row justify-content-md-center">
            <div class="col-md-auto">
                <button type="submit" class="btn dento-btn">Login</button>
            </div>
            <div class="col-md-auto">
                <a href="<?php echo $auth_url?>" class="btn dento-btn btn-outline-warning">Registr via google</a>
            </div>
        </div>
    </form>
</div>

