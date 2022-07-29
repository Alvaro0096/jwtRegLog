<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once('../vendor/autoload.php');

if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array("message" => "Token not found in request"));
    exit;
}

$jwt = $matches[1];
if (!$jwt) {
    
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array("message" => "No token was able to be extracted from the authorization header"));
    exit;
}

$secretKey  = 'MYKEY';
$token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
$now = time();
$serverName = "your.domain.name";

if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()) 
{
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(array("message" => "Expired Token"));
    exit;
}

echo "Llega la verificacion";

// Show the page
