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
            $reservations=$reservationQuery->fetch();
            if($reservationQuery->rowCount()<1){
                $errors['reservation'] = 'This reservation does not exist.';
                $_SESSION['errors'] = $errors;
                header('Location: personal.php');
            };
            if($reservations['id_user']!=$_SESSION['user_id']){
                if(!$isAdmin){
                    $errors['reservation'] = 'It is not your reservation';
                    $_SESSION['errors'] = $errors;
                    header('Location: personal.php');
                }else{
                    $ediable = true;
                    $_SESSION['id_res']=$reservations['id_res'];
                }
            }else{
                $ediable=true;
                $_SESSION['id_res']=$reservations['id_res'];
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
$timestampStart = strtotime($reservations['start_event']);
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
$notFree=false;
$id_ser=[];
if(!empty($_POST) and empty($errors)){
  if(checkCSRF($_SERVER['PHP_SELF'], $_POST['csrf'])) {
        #kontrola pomoci regilarniho vyrazu datum a cas
        if (preg_match('/[0-9-]{10} [0-9:]{8}/', $_POST['date'] . ' ' . $_POST['time'])) {
            $time = strtotime($_POST['date'] . ' ' . $_POST['time']);
            $startTime = date("Y-m-d H:i:s", $time);
            $endTime = date("Y-m-d H:i:s", strtotime('+30 minutes', $time));
        } else {
            $errorsForm['date'] = 'Invalid date/time format';
        }
        #endregion kontrola datumu

        #kontrola popisu
        $description = htmlspecialchars(trim(@$_POST['description']));
        if (strlen($description) > 255) {
            $errors['description'] = 'Max number of symbols - 255';
        }
        #konec kontroly popisu

      #kontrola sluzby
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

        #dalsi kontrola datumu
        #pokud neni mensi nez dnesni datum
        if ($startTime < date('Y-m-d H:i:s', time())) {
            $errorsForm['date'] = 'The day selected is less than today.';
        } else {
            #natahnime vsechny rezervace na tento den
            $selectReserv = $db->prepare('SELECT * FROM reservation_sem WHERE(start_event LIKE :date);');
            $selectReserv->execute([':date' => date('Y-m-d', $time) . '%']);
            $reservationsArray = $selectReserv->fetchAll();
            foreach ($reservationsArray as $item) {
                if ($item['start_event'] === $startTime) {
                    if ($item['id_res'] != $_SESSION['id_res']) {
                        $notFree = true;
                        $newArray = Array(substr($item['start_event'], 11));
                    }
                }
            }
        }
        if ($notFree) {
            $string = '';
            foreach (array_diff($poosibleTime, $newArray) as $item) {
                $string .= ' ' . substr($item, 0, 5) . ',';
            }
            $errorsForm['date'] = 'We are sorry, but this term was already taken by somebody. At (' . $_POST['date'] . ') this time: ' . $string . ' is still free!';
        }
        #konec kontroly unikatnosti datumu

        #endregion porovnani s dnesnim datumem
        #endregion

        if (empty($errorsForm)) {
            $updateQuery = $db->prepare('UPDATE reservation_sem SET start_event=:startTime, end_event=:endTime, comment=:description, id_ser=:id_ser WHERE id_res=:id LIMIT 1; ');
            $updateQuery->execute([
                ':startTime' => $startTime,
                ':endTime' => $endTime,
                ':description' => $description,
                ':id_ser' => json_encode($id_ser),
                ':id' => $_SESSION['id_res']
            ]);
            unset($_SESSION['id_res']);
            $_SESSION['success'] = 'You just update your reservation!';
            header('Location: personal.php');
        }
    }
else{
        $errorsForm['csrf']='Invalid CSRF token.';
    }
}
function getSerName($a, $db, $resID){
    $selectServices=$db->prepare('SELECT * FROM reservation_sem JOIN services_sem ON services_sem.id_ser=:id WHERE reservation_sem.id_res=:id_res LIMIT 1;');
    $selectServices->execute([
        ':id'=>$a,
        ':id_res'=>$resID
    ]);
    $services=$selectServices->fetchAll(PDO::FETCH_ASSOC);
    return $services[0];
}


#endregion zpracovani formulare
include './inc/header.php';
?>
<script>
    $( document ).ready(function() {
        $( window ).load(function() {
            let checked = [];
            $('input:checkbox:checked').each(function() {
                checked.push($(this).val());
                $('#radioValue').val(checked);
            });
           $('input:checkbox').each(function () {
               if($(this).attr("checked") != 'checked') {
                    checked.forEach((element) => {
                        if(element ===$(this).val()){
                            $(this).closest('.element').remove();
                        };
                    })
               }
           });
        });

        $("input").on( "click", function() {
            let checked = [];
            $('input:checkbox:checked').each(function() {
                checked.push($(this).val());
                $('#radioValue').val(checked);
            })});
    });
</script>
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
                            <form role="form" method="post" name="reservationEditForm">
                                <input type="hidden" name="csrf" value="<?php echo (getCSRF($_SERVER['PHP_SELF'])); ?>">
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
                                            <input type="date" class="form-control" id="date" value="<?php echo htmlspecialchars(date('Y-m-d', $timestampStart)); ?>" name="date"/>
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
                                            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars(@$reservations['comment']); ?>" placeholder="<?php echo htmlspecialchars(@$reservations['comment']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="service">
                                                Service:
                                            </label>
                                            <button type="button" class="showMe btn btn-light" data-toggle="modal" data-target="#serviceModalWindow">
                                                Choose a service
                                            </button>
                                            <input type="hidden" id="radioValue" name="serName" value="<?php echo htmlspecialchars(@$_POST['serName']);?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" name="submit" id="submit">
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

<!-- Second modal window -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="serviceModalWindow">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Chose a service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id='service' class="col-12 col-md-12">
                <div class="text-center">
                    <h4 class="text-dark mb-4">You can choose one service from this list</h4>
                </div>
                    <?php

                    foreach (json_decode($reservations['id_ser']) as $item)
                    {
                        $servis = getSerName($item, $db, $reservations['id_res']);
                        echo '                          
                          <div class="element">
                           <input checked="checked" type="checkbox" name="service"  value="'.htmlspecialchars(@$servis['id_ser']).'" id="'.htmlspecialchars(@$servis['id_ser']).'">
                           <label for="'.htmlspecialchars(@$servis['name']).'">'.htmlspecialchars(@$servis['name']).'</label>
                          </div>';
                    }
                    foreach ($services as $service)
                    {
                        echo '                          
                          <div class="element">
                           <input type="checkbox" name="service"  value="'.htmlspecialchars(@$service['id_ser']).'" id="'.htmlspecialchars(@$service['id_ser']).'">
                           <label for="'.htmlspecialchars(@$service['name']).'">'.htmlspecialchars(@$service['name']).'</label>
                          </div>';
                    }
                    ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Second modal window end-->

<?php
include 'inc/footer.php';
?>
</body>
</html>
