<?php
date_default_timezone_set('Europe/Prague');

$ediable=false;
$isAdmin = false;
$pageTitle = 'Edit service page';
$id = '';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if(!$isAdmin){
    $isAdmin = false;
}
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}
#konec kontroly

#vstupni kontorla
$errors = [];
if(isset($_GET)){
    if(key($_GET)==='id' or key($_GET)==='remove'){
        if(is_numeric(@$_GET['id']) or is_numeric(@$_GET['remove'])){
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $remove = false;
            }else{
                $id=$_GET['remove'];
                $remove = true;
            }
            $reservationQuery=$db->prepare('SELECT * from reservation_sem WHERE id_res=:id and historical=\'0\' and id_user=:id_user LIMIT 1;');
            $reservationQuery->execute([
                ':id'=>$id,
                'id_user'=>$_SESSION['user_id']
            ]);
            $reservation=$reservationQuery->fetch();
            if($reservationQuery->rowCount()<1){
                $errors['reservation'] = 'This reservation does not exist.';
                $_SESSION['errors'] = $errors;
                header('Location: personal.php');
            };
            if($reservation['id_user']!=$_SESSION['user_id']){
                if(!$isAdmin){
                    $errors['reservation'] = 'It is not your reservation';
                    $_SESSION['errors'] = $errors;
                    header('Location: personal.php');
                }else{
                    $ediable = true;
                    $_SESSION['id_res']=$reservation['id_res'];
                }
            }else{
                $ediable=true;
                $_SESSION['id_res']=$reservation['id_res'];
            }

        }else{
            $errors['parametr'] = 'Invalid parameter';
            $_SESSION['errors'] = $errors;
            #pokud je chybny parametr get
            header('Location: personal.php');
        }
    }else{
        $errors['parametr'] = 'Invalid parameter';
        $_SESSION['errors'] = $errors;
        #pokud je chybny parametr get
        header('Location: personal.php');
    }
}else{
    $errors['parametr'] = 'Invalid parameter';
    $_SESSION['errors'] = $errors;
    #pokud neobsahuje zadny pozadavek
    header('Location: personal.php');
}
#endregion vstupni kontrola

#mazani/editace prispevku
if($ediable){
    if($remove){
        $deleteQuery=$db->prepare('DELETE from reservation_sem where id_res=:id;');
        $deleteQuery->execute([
            ':id'=>$id
        ]);
        $_SESSION['success']='You just remove your reservation! Reservation id: '.$id.'';
        header('Location: personal.php');
    }else{
        $selectQuery=$db->prepare('SELECT * from services_sem;');
        $selectQuery->execute([]);
        $services=$selectQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
#endregion mazani/editace prispevku

#dostaneme timestamp rezervace
$timestampStart = strtotime($reservation['start_event']);
#konec timestamp

#nadefinujeme array z hodnotami casu rezervace
$poosibleTime = ['07:00:00','07:30:00', '08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00',
                 '10:30:00', '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00',
                 '13:30:00', '14:00:00', '14:30:00', '15:00:00', '15:30:00'];
#endregion

#funkce ktera dostava hodnoty vsech moznych rezervaci a porovna se skutecnou hodnotou na zaklade naplni <option> a prida atribut selected
function strTime($a, $b){
    if($a === $b){
        if(strlen($a)===7){
            echo ' <option selected value="'.$a.'">'.substr($a, 0,4).'</option>';
        }else{
            echo ' <option selected value="'.$a.'">'.substr($a, 0,5).'</option>';
        };
    }else{
        if(strlen($a)===7){
            echo ' <option value="'.$a.'">'.substr($a, 0,4).'</option>';
        }else{
            echo ' <option value="'.$a.'">'.substr($a, 0,5).'</option>';
        };
    }
}
#konec fce

#region zpracovani formulare
$errorsForm=[];
if(!empty($_POST) and empty($errors)){
    #kontrola pomoci regilarniho vyrazu datum a cas
    if(preg_match('/[0-9-]{10} [0-9:]{8}/', $_POST['date'].' '.$_POST['time'])){
        $time = strtotime($_POST['date'].' '.$_POST['time']);
        $startTime = date("Y-m-d H:i:s", $time);
        $endTime = date("Y-m-d H:i:s", strtotime('+30 minutes', $time));
    }else{
        $errorsForm['date'] = 'Invalid date/time format';
    }
    #endregion kontrola datumu

    #kontrola popisu
    $description = htmlspecialchars(trim(@$_POST['description']));
    if(strlen($description)>255){
        $errors['description']='Max number of symbols - 255';
    }
    #konec kontroly popisu

    #region kontrola sluzby
    $serviceId = trim(@$_POST['service']);
    if(empty($serviceId)){
        $errors['service']='Service name is not correct.';
    }else{
        #kontrola existence sluzby
        $serviseQuery=$db->prepare('SELECT * FROM services_sem WHERE id_ser=:id LIMIT 1;');
        $serviseQuery->execute([
            ':id'=>$serviceId
        ]);
        if($serviseQuery->rowCount()<=0){
            $errors['service']='Service name is not correct.';
        }
        #endregion kontrola existence sluzby
    }
    #endregion kontrola sluzby

    #dalsi kontrola datumu
        #pokud neni mensi nez dnesni datum
    if($startTime<date('Y-m-d H:i:s', time())){
        $errorsForm['date']= 'The day selected is less than today.';
    }else {
        #natahnime vsechny rezervace na tento den
        $selectReserv = $db->prepare('SELECT * FROM reservation_sem WHERE(start_event LIKE :date);');
        $selectReserv->execute([':date' => date('Y-m-d', $time) . '%']);
        $reservationsArray = $selectReserv->fetchAll();
        foreach ($reservationsArray as $item) {
            if ($item['start_event'] === $startTime) {
                if ($item['id_res'] != $_SESSION['id_res']) {
                    $notFree = true;
                    $newArray = Array(substr($item['start_event'],11));
                }
            }
        }
    }
    if($notFree){
        $string = '';
        foreach (array_diff($poosibleTime, $newArray) as $item){
            $string .= ' '.substr($item, 0,5).',';
        }
        $errorsForm['date']='We are sorry, but this term was already taken by somebody. At ('.$_POST['date'].') this time: '.$string.' is still free!';
    }
        #konec kontroly unikatnosti datumu

        #endregion porovnani s dnesnim datumem
    #endregion

    if(empty($errorsForm)){
        $updateQuery=$db->prepare('UPDATE reservation_sem SET start_event=:startTime, end_event=:endTime, comment=:description, id_ser=:id_ser WHERE id_res=:id LIMIT 1; ');
        $updateQuery->execute([
                ':startTime'=>$startTime,
                ':endTime'=>$endTime,
                ':description'=>$description,
                ':id_ser'=>$serviceId,
                ':id'=>$_SESSION['id_res']
        ]);
        unset($_SESSION['id_res']);
        $_SESSION['success']='You just update your reservation!';
        header('Location: personal.php');
    }
}
#endregion zpracovani formulare
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
                            <form role="form" method="post">
                                <?php
                                if(!empty($errorsForm)){
                                    echo '
                                       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                                    ×
                                       </button>
                                       <div class="alert alert-dismissable alert-warning">
                                       <h4 class="text-center">
                                             We found this mistakes!
                                        </h4>';
                                        foreach ($errorsForm as $error){
                                            echo '<p class="text-danger">'.$error.'</p>';
                                        }
                                        echo '</div>' ;}
                                ?>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="reservationDate">
                                                Reservation date:
                                            </label>
                                            <input type="date" class="form-control" id="exampleInputEmail1" value="<?php echo date('Y-m-d', $timestampStart); ?>" name="date"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="reservationDate">
                                                Reservation time:
                                            </label>
                                            <select name="time" id="time" class="form-control">
                                                <?php
                                                $time = date('H:i:s', $timestampStart);
                                                foreach ($poosibleTime as $value){
                                                    strTime($value, $time);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="description">
                                                Description:
                                            </label>
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars(@$reservation['comment']); ?>" placeholder="<?php echo htmlspecialchars(@$reservation['comment']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="service">
                                                Service:
                                            </label>
                                            <select name="service" id="serviceSelect" class="form-control">
                                                <?php
                                                foreach ($services as $service){
                                                    if($service['id_ser']===$reservation['id_ser']){
                                                        echo '<option selected value="'.htmlspecialchars($service['id_ser']).'">'.htmlspecialchars($service['name']).'</option>';
                                                    }else{
                                                        echo '<option value="'.htmlspecialchars($service['id_ser']).'">'.htmlspecialchars($service['name']).'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                    <button type="submit" class="btn btn-secondary">
                                        <a class="text-decoration-none text-white" href="personal.php?old=false"">Cancel</a>
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
