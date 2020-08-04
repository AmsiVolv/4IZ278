<?php
$isAdmin = false;
$pageTitle = 'Admin panel';
require_once './inc/user.php';


#kontrolujeme pokud je adminem
if(!$isAdmin){
    header('Location: index.php');
}
#konec kontroly admina

if(!empty($_POST)){
    #zpracovani formulare
    #existence uzivetele
    $errors=[];
    if(!empty($_POST['id_user'])){
        if (checkCSRF($_SERVER['PHP_SELF'], $_POST['csrf'])) {
            if(is_numeric($_POST['id_user'])){
                $selectQuery=$db->prepare('SELECT * from users_sem WHERE id_user =:id LIMIT 1;');
                $selectQuery->execute([
                    ':id'=>htmlspecialchars($_POST['id_user'])
                ]);
                if($selectQuery->rowCount()>0){
                    $id = htmlspecialchars($_POST['id_user']);
                }else{
                    $errors['id']='ID out of range.';
                }
            }else{
                $errors['id']='ID out of range.';
            }

    #endregion existence uzivatele
    #kontrola akce
            $disableStatus = false;
            $removeStatus = false;
            $showStatus = false;
            if($_POST['userOptiion'] === 'disable' || $_POST['userOptiion'] === 'remove' || $_POST['userOptiion'] === 'show'){
                $action = $_POST['userOptiion'];
                if($action==='disable'){
                    $disableStatus = true;
                }elseif($action === 'remove'){
                    $removeStatus = true;
                }elseif ($action === 'show'){
                    $showStatus = true;
                }
            }else{
                $errors['action']='Action type invalid';
            }
        }else{
            $errors['id']='ID is empty.';
        }
        }else{
        $errors['csrf']='Invalid CSRF token.';
       }
   #endregion kontrola akce
    if(empty($errors)){
        if($disableStatus){
            $disableQuery = $db->prepare('UPDATE users_sem SET active=\'0\' WHERE id_user=:id LIMIT 1;;');
            $disableQuery->execute([
                ':id'=>$id
            ]);
        }
        if($removeStatus){
            $removeQuery = $db->prepare('DELETE from users_sem WHERE id_user=:id LIMIT 1;');
            $removeQuery->execute([
                ':id'=>$id
            ]);
        }
        if($showStatus){
            $selectQuery=$db->prepare('SELECT * from reservation_sem WHERE id_user=:id and historical=\'0\';');
            $selectQuery->execute([
                    ':id'=>$id
            ]);
            $reservations=$selectQuery->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    #endregion zpracovani formulare
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

include './inc/header.php';

?>

<html>
<body>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script>
    $(document).ready(function(){

        $("#selUser").select2({
            ajax: {
                url: "./inc/ajaxfile.php",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    });

</script>

<div class="section-heading">
    <h2>Edit user page</h2>
    <div class="line"></div>
</div>


<div class="container-fluid">
    <div class="row justify-content-center mt-">
        <div class="col-md-10 card shadow-lg o-hidden border-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex bd-highlight align-self-baseline">
                        <div class="p-1 w-100 bd-highlight">
                            <h3 class="text-center">
                                List of your actions
                            </h3>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <form name="editUser" action="editUser.php" method="post">
                                            <input type="hidden" name="csrf" value="<?php echo (getCSRF($_SERVER['PHP_SELF'])); ?>">
                                            <select id='selUser' name="id_user" class="form-control" required>
                                                <option value='0'>- Search user -</option>
                                            </select>
                                            <select name="userOptiion" id="userOptiion" class="form-control mt-2" required>
                                                <option value="remove">Remove</option>
                                                <option value="disable">Disable</option>
                                                <option value="show">Show reservations</option>
                                            </select>
                                            <div class="mt-2 mb-5">
                                                <button class="btn btn-primary" type="submit" id="submit" name="submit">
                                                    Submit
                                                </button>
                                                <button class="btn btn-secondary" type="cancel" id="cancel" name="cancel">
                                                    <a href="adminpanel.php">
                                                        Cancel
                                                    </a>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <?php
                                        if(@$showStatus){
                                            echo '
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
                        <tbody> ';
                                        foreach ($reservations as $reservation){
                                            $timestampStart = strtotime($reservation['start_event']);
                                            $timestampEnd = strtotime($reservation['end_event']);
                                            echo '                        
                                    <tr>
                                        <td>
                                            '.htmlspecialchars($reservation['id_res']).'
                                        </td>
                                        <td class="text-center">
                                           '.date('d.m.y H:i', $timestampStart).'  -  '.date('d.m.y H:i', $timestampEnd). '
                                        </td>
                                         <td>
                                         <ul>';
                                            foreach (json_decode($reservation['id_ser']) as $item){
                                                $servis = getSerName($item, $db, $reservation['id_res']);
                                                echo '<span><li title="'.htmlspecialchars($servis['description']).'">'.htmlspecialchars($servis['name']).'</li></span>';
                                            }
                                            echo'</ul>
                                            </td>
                                            <td>
                                            '.htmlspecialchars($reservation['comment']).'
                                        </td>
                                        <td class="text-center"><a class="text-success pr-2" href="reservationedit.php?id=5">Edit</a><a class="text-danger pl-2" href="reservationedit.php?remove=5">Remove</a>
                                        </td>
                                    </tr>';
                                        }}else{
                                            '';
                                        }
                                        echo '</tbody>
                                           </table>';
                                        ?>
                                    </div>
                                </div>
                            </div>
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