<?php
require_once './inc/user.php';
require 'vendor/autoload.php';
$g_client = new Google_Client();

$g_client->setClientId("736932354364-h8860s2nu8rk72uqjbe9kma4fs03issj.apps.googleusercontent.com");
$g_client->setClientSecret("qoVxCM8j3HWPsYpcEgk6YALJ");
$g_client->setRedirectUri("https://eso.vse.cz/~volv02/semestralka/g-callback.php");
$g_client->setScopes("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");

if(isset($_GET['code'])){

    $token = $g_client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token']=$token;
}

$oAuth = new Google_Service_Oauth2($g_client);
$userData = $oAuth->userinfo_v2_me->get();

//zpracovani dat -> start

//nejperve se pokusime najit podle google_id
$query=$db->prepare('SELECT * from users_sem WHERE google_id=:googleId LIMIT 1;');
$query->execute([
    ':googleId'=>$userData['id']
]);

if($query->rowCount()>0){
    //uzivatel je v DB
    $user=$query->fetch(PDO::FETCH_ASSOC);
}else{
    //pokud nenašli jsme uživatele podle ID pokusíme se najít ho pomocí emailu
    $query=$db->prepare('SELECT * from users_sem WHERE email=:googleEmail LIMIT 1;');
    $query->execute([
        ':googleEmail'=>$userData['email']
    ]);
}

if($query->rowCount()>0){
    //email uzivatele je v DB, pridame mu ID
    $user = $query->fetch(PDO::FETCH_ASSOC);

    $updateQuery = $db->prepare('UPDATE users_sem SET google_id=:googleId WHERE id_user=:id LIMIT 1;');
    $updateQuery->execute([
        ':googleId'=>$userData['id'],
        ':id'=>$user['id_user']
    ]);
}else{
    //Pokud uzivatel vubec neni v DB, tak pridame ho

    $insertQuery=$db->prepare('INSERT INTO users_sem (name, surname, email, google_id) VALUES (:name, :surname, :email, :google_id);');
    $insertQuery->execute([
        ':name'=>$userData['givenName'],
        ':surname'=>$userData['familyName'],
        ':email'=>$userData['email'],
        ':google_id'=>$userData['id']
    ]);
}
//zpracovani dat -> end

//nacteme a provedeme prihlaseni uzivatele
$query=$db->prepare('SELECT * from users_sem WHERE google_id=:googleId LIMIT 1;');
$query->execute([
    ':googleId'=>$userData['id']
]);
$user=$query->fetch(PDO::FETCH_ASSOC);

if(!empty($user)){
    //prihlaseni uzivatele do db
    $_SESSION['user_id']=$user['id_user'];
    $_SESSION['user_name']=$user['name'];
}


header('Location: index.php');
