<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './requestOne.class.php';
include_once './validate.php';

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array('Error' => 'Token not found in request.'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$id = $data['id'];

if($id == '' || empty($id)){
    echo json_encode(array('Error' => 'ID cannot be empty.'));
    exit;
}

$validate = new Validate();
$validate->validateToken();

if($validate->tokenCheck){
    $request = new RequestOne();
    $request->getOneCard($id);
} else {
    echo json_encode(array('Error' => 'The token is not valid.'));
}

?>