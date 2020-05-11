<?php
$isAdmin = false;
$pageTitle = 'New service page';
$serviceId = '';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if(!$isAdmin){
    header('Location: index.php');
}
#konec kontroly admina
$selectQuery=$db->prepare('SELECT * FROM services_sem WHERE id_ser=:id LIMIT 1;');


#region nacteni existujeciho prespevku
if(!empty($_REQUEST['id']) and is_numeric($_REQUEST['id'])){
    #kontrola existence prispevku
    $selectQuery->execute([
        ':id'=>$_REQUEST['id']
    ]);
    #konec kontroly
    if($selectQuery->rowCount()>0){
        $id = $_REQUEST['id'];
        $postQuery=$db->prepare('SELECT * FROM services_sem WHERE id_ser=:id');
        $postQuery->execute([
            ':id'=>$id
        ]);
        $post=$postQuery->fetch();
        #naplnujeme pomocne promeny
        $serviceId = $post['id_ser'];
        $name = $post['name'];
        $price = $post['price'];
        $description = $post['description'];
        #konec
    }else{
        header('Location: pricing.php?range=failed');
        exit();
    }
}elseif (!is_numeric(@$_REQUEST['id']) and !empty(@$_REQUEST['id'])){
    header('Location: pricing.php?status=failed');
    exit();
}
#region konec nacteni existujeciho prespevku

#region mazani prespevku
if(!empty($_REQUEST['remove']) and is_numeric($_REQUEST['remove'])){
    #region kontrola existence prespevku
    $selectQuery->execute([
        ':id'=>$_REQUEST['remove']
    ]);
    #endregion kontrola existence prespevku
    if($selectQuery->rowCount()>0){
        #mazani
        $removeId = $_REQUEST['remove'];
        $removeQuery=$db->prepare('DELETE FROM services_sem WHERE id_ser=:id;');
        $removeQuery->execute([
            ':id'=>$removeId
        ]);
        header('Location: pricing.php');
        exit();
        #konec mazani
    }else{
        header('Location: pricing.php?range=failed');
        exit();
    }
}elseif (!is_numeric(@$_REQUEST['remove']) and !empty(@$_REQUEST['remove'])) {
    header('Location: pricing.php?status=failed');
    exit();
}
#endregion mazani prespevku

$errors=[];
if(!empty($_POST)){
    #region kontrola formulare
        #kontrola nazvu sluzby
        $name = trim($_POST['service_name']);
        if(empty($name)){
            $errors['name']='Service name is not correct.';
        }
        #konec kontroly

        #kontrola ceny
        if(is_numeric($_POST['service_price'])){
            $price = $_POST['service_price'];
        }else{
            $errors['price']='Service price is not correct.';
        }
        #konec kontroly ceny

        #kontrola popisu sluzby
        $description = trim($_POST['service_description']);
        if(empty($description)){
            $errors['description']='Service description is not correct.';
        }
        #konec kontroly popisu sluzby
    #konec kontroly formulare

    if(empty($errors)){
        if($serviceId){
            #aktualizace existujeciho prespevku
            $updateQuery=$db->prepare('UPDATE services_sem SET name=:name, price=:price, description=:description WHERE id_ser=:id LIMIT 1;');
            $updateQuery->execute([
                ':name'=>$name,
                ':price'=>$price,
                ':description'=>$description,
                ':id'=>$serviceId
            ]);
            #konec aktualizace
        }else{
            #vkladani noveho prispevku
            $serviceQuery=$db->prepare('INSERT INTO services_sem (name, price, description) VALUES (:name, :price, :description);');
            $serviceQuery->execute([
                ':name'=>$name,
                ':price'=>$price,
                ':description'=>$description
            ]);
            #konec vkladani noveho prispevku
        }
        header('Location: pricing.php');
        exit();
    }
}
include './inc/header.php';
?>
<section class="dento-about-us-area section-padding-100-0">
    <?php
        if(!empty($errors)){
            foreach ($errors as $error){
                echo $error.'</br>';
            }
        }
    ?>
    <div class="container">
        <form method="post">
            <div class="form-group row justify-content-center">
                <label class="col-1 col-form-label mr-50" for="Service_name">Name: </label>
                <div class="col-5">
                    <input required id="service_name" name="service_name" placeholder="" type="text" class="form-control"  value="<?php echo htmlspecialchars(@$name);?>" />
                </div>
            </div>
            <div class="form-group row justify-content-center">
                <label for="service_price" class="col-1 col-form-label mr-50">Price: </label>
                <div class="col-5">
                    <input required id="service_price" name="service_price" placeholder="" type="number" class="form-control" value="<?php echo htmlspecialchars(@$price);?>" />
                </div>
            </div>
            <div class="form-group row justify-content-center">
                <label for="service_description" class="col-1 col-form-label mr-50">Description: </label>
                <div class="col-5">
                    <textarea required type="text" id="service_description" name="service_description" placeholder="" cols="40" rows="5" class="form-control" value=""><?php echo htmlspecialchars(@$description);?></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-15 mb-100">
                    <a href="pricing.php" class="btn dento-btn booking-btn mr-50">Back <i class="fa fa-angle-double-right"></i></a>
                    <button name="submit" type="submit" class="btn dento-btn booking-btn registr">Submit</button>
            </div>
        </form>
    </div>
</section>
<?php
include 'inc/footer.php';
?>
</body>

</html>