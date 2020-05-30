<?php
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
    header('Location: personal.php');
}
#konec kontroly
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
            $reservationQuery=$db->prepare('SELECT * from reservation_sem WHERE id_res=:id LIMIT 1;');
            $reservationQuery->execute([
                ':id'=>$id
            ]);
            $reservation=$reservationQuery->fetch();

            if($reservation['id_user']!=$_SESSION['user_id']){
                if(!$isAdmin){
                    $errors['reservation'] = 'It is not your reservation';
                    header('Location: personal.php');
                }else{
                    $ediable = true;
                }
            }else{
                $ediable=true;
            }

        }else{
            $errors['parametr'] = 'Invalid parameter';
            #pokud je chybny parametr get
            header('Location: personal.php');
        }
    }else{
        $errors['parametr'] = 'Invalid parameter';
        #pokud je chybny parametr get
        header('Location: personal.php');
    }
}else{
    $errors['parametr'] = 'Invalid parameter';
    #pokud neobsahuje zadny pozadavek
    header('Location: personal.php');
}


if($ediable){
    if($remove){
        $deleteQuery=$db->prepare('DELETE from reservation_sem where id_res=:id;');
        $deleteQuery->execute([
            ':id'=>$id
        ]);
        $_SESSION['success']='You just remove your reservation! Reservation id: '.$id.'';
        header('Location: personal.php');
    }else{

    }
}

var_dump($reservation);
$timestampStart = strtotime($reservation['start_event']);

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
                            <form role="form">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">
                                        Reservation date:
                                    </label>
                                    <input type="date" class="form-control" id="exampleInputEmail1" value="<?php echo date('Y-m-d', $timestampStart); ?>"/>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
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
