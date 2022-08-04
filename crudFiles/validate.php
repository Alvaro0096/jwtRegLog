<?php
declare(strict_types=1);
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Validate{
    public $tokenCheck = false;

    public function validateToken(){

        if(!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array('Error' => 'Token not found in request.'));
            exit;
        }
        
        $jwt = $matches[1];

        if(!$jwt){
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array('Error' => 'No token was able to be extracted from the authorization header.'));
            exit;
        }
        
        $secret_key  = 'MYKEY';
        $token = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        $now = new DateTimeImmutable();
        $issuer_claim = "http://localhost";
        
        if($token->iss !== $issuer_claim || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(array('Error' => 'Expired Token. You must login again.'));
            exit;
        }
        
        if(empty($token->data)){
            echo json_encode(array('Error' => 'Unable to access. The token data is empty.'));
            exit;
        }
        
        $this->tokenCheck = true;
    }
}

$a = new Validate();
$a->validateToken();

?>
