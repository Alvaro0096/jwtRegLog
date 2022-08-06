<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './update.class.php';
include_once './validate.php';

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array('Error' => 'Token not found in request.'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'PUT'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$cardName = $data['cardName'];
$cardImg = $data['cardImg'];
$cardSize = $data['cardSize'];
$cardId = $data['cardId'];

if($cardName == '' || empty($cardName) 
    || $cardImg == '' || empty($cardImg) 
    || $cardSize == '' || empty($cardSize) 
    || $cardId == '' || empty($cardId)){
    echo json_encode(array('Error' => 'Card name, card image and card size cannot be empty. An id is required to select the card to modify.'));
    exit;
}

$verifyToken = new Validate();
$verifyToken->validateToken();

if($verifyToken->resultArr['valid'] === true && $verifyToken->resultArr['userType'] === 'ADMIN'){
    $request = new Update();
    $request->updateData($cardName, $cardImg, $cardSize, $cardId);
} else {
    echo json_encode(array('Error' => "The token is invalid or the user don't have permissions."));
}

?>