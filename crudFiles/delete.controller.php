<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './delete.class.php';
include_once './validate.php';

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array('Error' => 'Token not found in request.'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$cardId = $data['cardId'];

if($cardId == '' || empty($cardId)){
    echo json_encode(array('Error' => 'Id cannot be empty. An Id is required to select the card to delete.'));
    exit;
}

$validate = new Validate();
$validate->validateToken();

if($validate->tokenCheck){
    $request = new Delete();
    $request->deleteData($cardId);
} else {
    echo json_encode(array('Error' => 'The token is not valid.'));
}

?>