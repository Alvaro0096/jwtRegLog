<?php
include_once '../config/database.php';
include_once '../config/headers.php';
include_once './request.class.php';

if(!$_SERVER['HTTP_AUTHORIZATION']){
    header('HTTP/1.0 400 Bad Request');
    echo 'Token not found in request';
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo 'Invalid REQUEST METHOD.';
    exit;
} 

$request = new Request();
$request->getData();

?>