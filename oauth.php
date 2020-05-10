<?php

require 'vendor/autoload.php';
//Step 1: Enter you google account credentials

$g_client = new Google_Client();

$g_client->setClientId("736932354364-h8860s2nu8rk72uqjbe9kma4fs03issj.apps.googleusercontent.com");
$g_client->setClientSecret("qoVxCM8j3HWPsYpcEgk6YALJ");
$g_client->setRedirectUri("https://eso.vse.cz/~volv02/semestralka/g-callback.php");
$g_client->setScopes("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");

//Step 2 : Create the url
$auth_url = $g_client->createAuthUrl();

//Step 3 : Get the authorization  code
$code = isset($_GET['code']) ? $_GET['code'] : NULL;

//Step 4: Get access token
if(isset($code)) {

    try {

        $token = $g_client->fetchAccessTokenWithAuthCode($code);
        $g_client->setAccessToken($token);

    }catch (Exception $e){
        echo $e->getMessage();
    }

    try {
        $pay_load = $g_client->verifyIdToken();


    }catch (Exception $e) {
        echo $e->getMessage();
    }

} else{
    $pay_load = null;
}

if(isset($pay_load)){
    var_dump(1);
}
