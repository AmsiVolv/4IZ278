<?php
require_once 'inc/user.php';

if(empty($_SESSION['user_id'])){
    $login = false;
}else{
    $login = true;
}

#region zpracování formuláře
#region kontrola sluzby
if($login) {
    if (isset($_POST)) {
        if (checkCSRF($_POST['csrfLocation'], $_POST['csrf'])) {
            $errors = [];
            $id_ser=[];
            if(preg_match('/^[0-9,]+$/',$_POST['serName'])){
                $arraySerId = explode(',', $_POST['serName']);
                foreach ($arraySerId as $value){
            #kontrola existence sluzby
                $serviseQuery = $db->prepare('SELECT * FROM services_sem WHERE id_ser=:id LIMIT 1;');
                $serviseQuery->execute([
                    ':id' => $value
                    ]);
                if ($serviseQuery->rowCount() > 0) {
                    array_push($id_ser,$value);
                } else {
                    $errors['service'] = 'Service name is not correct.';
                    var_dump($errors);
             #endregion kontrola existence sluzby
                }
              }
            }else{
                 $errors['id']='Invalid service ID';
            }


        #kontrola datumu
        if (DateTime::createFromFormat('Y-m-d H:i:s', $_POST['start']) !== FALSE) {
            $start = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['start']);
            if (DateTime::createFromFormat('Y-m-d H:i:s', $_POST['end']) !== FALSE) {
                $end = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['end']);
                if($start > $end){
                    $errors['date']="Wrong date section.";
                }elseif ($start <= DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"))){
                    $errors['date']="Wrong date section.";
                }
            }else{
                $errors['end_date']='Wrong end date.';
            }
        }else{
            $errors['start_date']='Wrong start date.';
        }
        #endregion kontrola datumu

        #kontrola popisu
        $description = trim(@$_POST['description']);
        if(strlen($description)>255){
            $errors['description']='Max number of symbols - 255';
        }
        #konec kontroly popisu

        #kontola max 3 rezervace u jednoho cloveka
        $selectQuery=$db->prepare('SELECT * from reservation_sem WHERE id_user=:id LIMIT 3;');
        $selectQuery->execute([
            ':id'=>$_SESSION['user_id']
        ]);
        if($selectQuery->rowCount()>4){
            $errors['reservation']='Max number of reservation is 3';
        }
        #endregion kontrola rezervace
        #konec kontroly formulare

        if(!empty($errors)){
            $_SESSION['errors']=$errors;
            header('Location: cal-reservation.php');
        }else{
            #pokud nejsou zadne chyby, tak provedeme prikaz
            $inserQuery=$db->prepare('INSERT INTO reservation_sem(start_event, end_event, id_user, id_ser, comment) VALUES(:start_event, :end_event, :id_user, :id_ser, :description);');
            $inserQuery->execute([
                ':start_event'=>$_POST['start'],
                ':end_event'=>$_POST['end'],
                ':id_user'=>$_SESSION['user_id'],
                ':id_ser'=>json_encode($id_ser),
                ':description'=> $description
            ]);
            $_SESSION['success']='You just made your reservation';
            header('Location: personal.php');
        }
        }else{
            $errors['csrf']='Invalid CSRF token';
            $_SESSION['errors']=$errors;
            header('Location: cal-reservation.php');
        }
    }
}else{
    $errors['login'] = 'First you need to sign up/sign in';
    $_SESSION['errors']=$errors;
    header('Location: cal-reservation.php');
}