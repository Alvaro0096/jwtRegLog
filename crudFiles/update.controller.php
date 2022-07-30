<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './update.class.php';

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo 'Token not found in request';
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo 'Invalid REQUEST METHOD.';
    exit;
} 

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$email = $data['email'];
$password = $data['password'];
$userType = $data['userType'];

if($email == '' || empty($email) || $password == '' || empty($password) || $userType == '' || empty($userType)){
    echo 'Email, password and userType cannot be empty.';
    exit;
}

$request = new Update();
$request->updateData($email, $password, $userType);

?>