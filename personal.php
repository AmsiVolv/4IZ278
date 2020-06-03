<?php
$isAdmin = false;
$pageTitle = 'Reservation';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if ($isAdmin) {
    $isAdmin=true;
}
#konec kontroly admina



#nacteme rezervace a sluzby
if(isset($_GET['old']) and $_GET['old']==='true'){
    $reservationQuery=$db->prepare('SELECT * FROM reservation_sem WHERE reservation_sem.id_user=:id and historical=\'1\' ORDER by start_event ASC;');
    $reservationQuery->execute([
        ':id'=>@$_SESSION['user_id']
    ]);
    $reservations=$reservationQuery->fetchAll();
}else{
    $reservationQuery=$db->prepare('SELECT * FROM reservation_sem WHERE reservation_sem.id_user=:id and historical=\'0\' ORDER by start_event ASC;');
    $reservationQuery->execute([
        ':id'=>@$_SESSION['user_id']
    ]);
    $reservations=$reservationQuery->fetchAll();
}

#kontrola na historicke rezervace
foreach ($reservations as $reservation){
    if($reservation['start_event']<date('Y-m-d H:i:s', time()) and $reservation['historical']==='0'){
        $updateQuery=$db->prepare('UPDATE `reservation_sem` SET `historical` = \'1\' WHERE `reservation_sem`.`id_res` =:id;');
        $updateQuery->execute([
                ':id'=>$reservation['id_res']
        ]);
        header('Location: personal.php');
    }
}
#endregion
function getSerName($a, $db, $resID){
    $selectServices=$db->prepare('SELECT * FROM reservation_sem JOIN services_sem ON services_sem.id_ser=:id WHERE reservation_sem.id_res=:id_res LIMIT 1;');
    $selectServices->execute([
        ':id'=>$a,
        ':id_res'=>$resID
    ]);
    $services=$selectServices->fetchAll(PDO::FETCH_ASSOC);
    return $services[0];
}

include './inc/header.php';
?>
<html>

<body>
<div class="section-heading">
    <h2>Personal area</h2>
    <div class="line"></div>
</div>
<div class="container-fluid">
    <div class="row justify-content-center mt-">
        <div class="col-md-10 card shadow-lg o-hidden border-0">
            <div class="row">
                <div class="col-md-9">
                    <div class="d-flex bd-highlight align-self-baseline">
                        <div class="p-1 w-100 bd-highlight">
                            <h3 class="text-center">
                                List of your reservations
                            </h3>
                        </div>
                        <div class="p-2 flex-shrink-1 bd-highlight">
                            <button type="button" class="btn btn-outline-info">
                                <?php
                                    if(isset($_GET) and @$_GET['old']==='true'){
                                        echo '
                                <a href="personal.php?old=false">
                                    Actual reservations
                                </a>
                                        ';
                                    }else{
                                        echo '
                                <a href="personal.php?old=true">
                                    Old reservations
                                </a>
                                        ';
                                    }
                                ?>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <?php

                    #pokud clovek ted udelal rezervaci, tak oznamime ho
                        if(isset($_SESSION['success'])) {
                            echo '
                    <div class="alert alert-dismissable alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            ×
                        </button>
                        <h4 class="text-center">
                            Congratulation!
                        </h4> ' . htmlspecialchars($_SESSION['success']) . '
                    </div>';
                            unset($_SESSION['success']);
                        }

                    #konec bloku

                    #pokud byly nejaky chyby u editace
                    if (isset($_SESSION['errors'])) {
                        echo '
                                       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                                    ×
                                       </button>
                                       <div class="alert alert-dismissable alert-warning">
                                       <h4 class="text-center">
                                             We found this mistakes!
                                        </h4>';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<p class="text-danger">' . $error . '</p>';
                        }
                        unset($_SESSION['errors']);
                        echo '</div>';
                    }
                    #konec bloku

                    #pokud u cloveka nejsoiu zadne rezervace tak zobrazime hlasku
                    if(empty($reservations)){
                        echo '
                    <div class="alert alert-dismissable alert-warning">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            ×
                        </button>
                        <h4>
                            Oops!
                        </h4> 
                           You currently have no active reservations. You can <strong><a href="cal-reservation.php">create it now!</a></strong>
                    </div>';
                        echo '
                </div>
                <div class="col-md-3">
                    <h3 class="text-center p-3">
                        Chat
                        <hr>
                    </h3>
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </div>';
                        exit();
                    }
                    #konec zobrazovani hlasky
                        ?>
                    <table class="table table-bordered table-hover mt-2 mb-5">
                        <thead>
                        <tr class="text-center">
                            <th>
                                #
                            </th>
                            <th>
                                Reservation period
                            </th>
                            <th>
                                Booked service
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;
                            foreach ($reservations as $reservation){

                                $timestampStart = strtotime($reservation['start_event']);
                                $timestampEnd = strtotime($reservation['end_event']);
                                echo
                                '
                                     <tr>
                                        <td>
                                            '.$index++.'
                                        </td>
                                        <td class="text-center">
                                           '.date('d.m.y H:i', $timestampStart).'  -  '.date('d.m.y H:i', $timestampEnd). '
                                        </td>
                                         <td>
                                         <ul>';
                                        foreach (json_decode($reservation['id_ser']) as $item){
                                            $servis = getSerName($item, $db, $reservation['id_res']);
                                            echo '<span><li title="'.$servis['description'].'">'.$servis['name'].'</li></span>';
                                         }
                                        echo'
                                            </ul>
                                            </td>
                                            <td>
                                            '.htmlspecialchars($reservation['comment']).'
                                        </td>
                                        <td class="text-center">';
                                            if(@$_GET['old']!='true'){
                                                echo '<a class="text-success pr-2" href="reservationedit.php?id='.$reservation['id_res'].'">Edit</a>';
                                            }
                                            echo '<a class="text-danger pl-2" href="reservationedit.php?remove='.$reservation['id_res'].'">Remove</a>
                                        </td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-3">
                    <h3 class="text-center p-3">
                        Chat
                        <hr>
                    </h3>
                    <div class="row">
                        <div class="col-md-12">
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
