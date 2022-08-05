<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './request.class.php';
include_once './validate.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array('Error' => 'Token not found in request.'));
    exit;
}

$verifyToken = new Validate();
$verifyToken->validateToken();

if($verifyToken->resultArr['valid'] === true){
    $request = new Request();
    $request->getCards();
} else {
    echo json_encode(array('Error' => 'The token is not valid.'));
}

?>