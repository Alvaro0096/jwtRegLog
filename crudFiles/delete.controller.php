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
    echo json_encode(array('Error' => 'An Id is required to select the card to delete.'));
    exit;
}

$verifyToken = new Validate();
$verifyToken->validateToken();

if($verifyToken->resultArr['valid'] === true && $verifyToken->resultArr['userType'] === 'ADMIN'){
    $request = new Delete();
    $request->deleteData($cardId);
} else {
    echo json_encode(array('Error' => "The token is invalid or the user don't have permissions."));
}

?>