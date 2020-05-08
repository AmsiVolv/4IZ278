<?php
#nacteme hlavicku
include './inc/header.php';


$errors = [];
if (!empty($_SESSION['user_id'])){
    //uživatel už je přihlášený, nemá smysl, aby se registroval
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    $name = trim(@$_POST['name']);
    #region zpracování formuláře
    #region kontrola jména
    if (empty($name)){
        $errors['name']='Name is not correct.';
    }
    #endregion kontrola jména
    #region kontrola prijmeni
    $surname = trim(@$_POST['surname']);
    if (empty($surname)){
        $errors['surnmae']='Surname is not correct.';
    }
    #endregion kontrola prijmeni
    #region kontrola emailu
    $email=trim(@$_POST['email']);
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['name']='This email adress is not correct.';
    }else{
        //kontrola, jestli již není e-mail registrovaný
        $mailQuery=$db->prepare('SELECT * FROM users_sem WHERE email=:email LIMIT 1;');
        $mailQuery->execute([
            ':email'=>$email
        ]);
        if ($mailQuery->rowCount()>0){
            $errors['name']='This email address already exist.';
        }
    }
    #endregion kontrola emailu
    #region kontrola tel.cisla
    $_POST['phone']=str_replace([' ','-','/'],'',trim($_POST['phone']));
    if ($_POST['phone']!='' && !preg_match("/^\+420[0-9]{9}$/",$_POST['phone'])) {
        $errors['phone'] = 'Number is not correct!';
    }
    #endregion kontrola tel.cisla
    #region kontrola hesla
    if(empty($_POST['password'] || (strlen($_POST['password']<5)))){
        $errors['password']='Password should be 8 characters at least';
    }
    if($_POST['password'] != $_POST['passwordTwo']){
        $errors['password']='The passwords you entered do not match';
    }
    #endregion kontrola hesla

    if(empty($errors)){
        #registrace uzivatele
        $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

        $userQuery=$db->prepare('INSERT INTO users_sem (name, surname, email, password) VALUES (:name, :surname, :email, :password);');
        $userQuery->execute([
                ':name'=> $name,
                ':surname'=> $surname,
                ':email'=> $email,
                ':password'=>$password
        ]);
    }

    //uživatele rovnou přihlásíme
    $_SESSION['user_id']=$db->lastInsertId();
    $_SESSION['user_name']=$name;

    //přesměrování na homepage
    header('Location: index.php');
//    exit();
}

?>

    <div class="col-12 col-md-12">
            <!-- Section Heading -->
            <div class="section-heading">
                <h2>Register now</h2>
                <div class="line"></div>
            </div>
            <!-- Form -->
            <form action="signup.php" method="post">
                <div class="row justify-content-md-center">
                    <div class="col-lg-2  mr-1">
                        <input type="text" name="name" id="name" class="form-control mb-30" placeholder="Your Name" required value="<?php echo htmlspecialchars(@$_POST['name']);?>">
                    </div>
                    <div class="col-lg-2">
                        <input type="text" name="surname" id="surname" class="form-control mb-30" placeholder="Your Surname" required value="<?php echo htmlspecialchars(@$_POST['surname']);?>">
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-4">
                        <input type="email" name="email" id="email" class="form-control mb-30" placeholder="Your Email" required value="<?php echo htmlspecialchars(@$_POST['email']);?>">
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-4">
                        <input type="tel" name="phone"  id="phone" class="form-control mb-30" placeholder="Phone number: +420111222333" pattern="[+420]{4}[0-9]{9}" required value="<?php echo htmlspecialchars(@$_POST['phone']);?>">
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-4">
                        <input type="password" name="password" id="password" class="form-control mb-30" placeholder="Password" required value="">
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-4">
                        <input type="password" name="passwordTwo" id="passwordTwo" class="form-control mb-30" placeholder="Password" required value="">
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-md-auto">
                        <button type="submit" class="btn dento-btn">Send Message</button>
                    </div>
                </div>
            </form>
        </div>

