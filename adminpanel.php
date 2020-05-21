<?php
$isAdmin = false;
$pageTitle = 'Admin panel';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if(!$isAdmin){
    header('Location: index.php');
}
#konec kontroly admina

include './inc/header.php';
?>