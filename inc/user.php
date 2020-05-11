<?php

session_start(); //spustíme session

require_once 'db/db.php'; //načteme připojení k databázi

#region kontrola, jestli je přihlášený uživatel platný
if (!empty($_SESSION['user_id'])){
    $userQuery=$db->prepare('SELECT id_user FROM `users_sem` WHERE id_user=:id and active=\'1\' LIMIT 1;');
    $userQuery->execute([
        ':id'=>$_SESSION['user_id']
    ]);
    if ($userQuery->rowCount()!=1){
        //uživatel už není v DB, nebo není aktivní => musíme ho odhlásit
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        header('Location: index.php');
        exit();
    }
}
#endregion kontrola, jestli je přihlášený uživatel platný
if (!empty($_SESSION['user_id'])) {
    $adminQuery = $db->prepare('SELECT id_user FROM users_sem where id_user=:id and role=\'admin\' LIMIT 1');
    $adminQuery->execute([
        ':id'=>$_SESSION['user_id']
    ]);
    if($adminQuery->rowCount()>0){
        $isAdmin=true;
    }
}

