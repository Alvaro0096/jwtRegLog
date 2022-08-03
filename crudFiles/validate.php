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
        
        $secretKey  = 'MYKEY';
        $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        $now = new DateTimeImmutable();
        $issuer_claim = "http://localhost";
        
        if($token->iss !== $issuer_claim || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(array('Error' => 'Expired Token.'));
            exit;
        }
        
        if(empty($token->data) || $token->data->userType !== 'Admin'){
            echo json_encode(array('Error' => 'Unable to access. User must be Admin type.'));
            exit;
        }

        $this->tokenCheck = true;
    }
}

$a = new Validate();
$a->validateToken();

?>
