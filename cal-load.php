<?php
require_once 'inc/user.php';

$data = array();

$loadQuery=$db->prepare('SELECT * FROM reservation_sem ORDER BY id_res;');
$loadQuery->execute();
$result = $loadQuery->fetchAll();

foreach ($result as $value){
    $data[] = array(
        'id'=> $value['id_res'],
        'start'=>$value['start_event'],
        'end'=>$value['end_event']
    );
}

//To json format
echo json_encode($data);
?>