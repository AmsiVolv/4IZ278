<?php
require_once 'inc/user.php';

if(isset($_POST)){
    $errors = [];

    #region zpracování formuláře
    #region kontrola sluzby
    $serviceName = trim(@$_POST['serName']);
    if (empty($serviceName)){
        $errors['service']='Service name is not correct.';
    }else{
        #kontrola existence sluzby
        $serviseQuery=$db->prepare('SELECT * FROM services_sem WHERE name=:name LIMIT 1;');
        $serviseQuery->execute([
            ':name'=>$serviceName
        ]);
        if($serviseQuery->rowCount()>0){
            $id_ser=$serviseQuery->fetch();
        }else{
            $errors['service']='Service name is not correct.';
        }
        #endregion kontrola existence sluzby
    }
    #endregion kontrola sluzby

    #kontrola datumu
    if (DateTime::createFromFormat('Y-m-d H:i:s', $_POST['start']) !== FALSE) {
            $start = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['start']);
        if (DateTime::createFromFormat('Y-m-d H:i:s', $_POST['end']) !== FALSE) {
            $end = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['end']);
            if($start > $end){
                $errors['date']="Wrong date section";
            }elseif ($start < DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"))){
                $errors['date']="Wrong date section";
            }
        }else{
            $errors['end_date']='Wrong end date';
        }
    }else{
        $errors['start_date']='Wrong start date';
    }
    #endregion kontrola datumu

    #kontrola popisu
    $description = trim(@$_POST['description']);
    #konec kontroly popisu

    #konec kontroly formulare
    if(!empty($errors)){
        header('Location: cal-reservation.php');
    }else{
        #pokud nejsou zadne chyby, tak provedeme prikaz
        $inserQuery=$db->prepare('INSERT INTO reservation_sem(start_event, end_event, id_user, id_ser, description) VALUES(:start_event, :end_event, :id_user, :id_ser, :description);');
        $inserQuery->execute([
            ':start_event'=>$_POST['start'],
            ':end_event'=>$_POST['end'],
            ':id_user'=>$_SESSION['user_id'],
            ':id_ser'=>$id_ser['id_ser'],
            ':description'=> $description
        ]);
        header('Location: cal-reservation.php');
    }
}