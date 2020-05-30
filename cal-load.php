<?php
require_once 'inc/user.php';

$data = array();

$loadQuery=$db->prepare('SELECT * FROM reservation_sem ORDER BY id_res;');
$loadQuery->execute();
$result = $loadQuery->fetchAll();

foreach ($result as $value){
    if(!isset($_SESSION['user_id']) or $value['id_user'] != $_SESSION['user_id']){
        $data[] = array(
            'id'=> $value['id_res'],
            'start'=>$value['start_event'],
            'end'=>$value['end_event'],
            'color'=> 'white',
            'title'=>'The term is already taken by someone else.',
            'editable'=> false,
            'textColor'=> 'red'
        );
    }else{
        $data[] = array(
            'id'=> $value['id_res'],
            'start'=>$value['start_event'],
            'end'=>$value['end_event'],
            'color'=> 'yellow',
            'title'=> 'This is your term',
            'editable'=> false,
            'textColor'=> 'black'
        );
    }
}
//To json format
echo json_encode($data);
?>