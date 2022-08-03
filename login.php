<?php
include_once './config/database.php';
include_once './config/headers.php';
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$email = '';
$password = '';
$userType = '';
$conn = null;

$databaseService = new DBConnection();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$userType = $data->userType;

$table_name = 'users';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

if($email == '' || empty($email) || $password == '' || empty($password)){
    echo json_encode(array('Error' => 'Email and Password must be completed.'));
    exit;
}

$query = "SELECT user_id, user_email, user_pass, user_type FROM " . $table_name . " WHERE user_email = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['user_id'];
    $email = $row['user_email'];
    $password2 = $row['user_pass'];
    $userType = $row['user_type'];

    if(password_verify($password, $password2)){
        $secret_key = "MYKEY";
        $issuer_claim = "http://localhost"; // this can be the servername
        $audience_claim = "http://localhost";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 6000; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "email" => $email, 
                "userType" => $userType   
        ));

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key, 'HS256');
        echo json_encode(
            array(
                "message" => "Successful login." . '<br>',
                "jwt" => $jwt,
                "id" => $id,
                "email" => $email,
                "userType" => $userType,
                "expireAt" => $expire_claim
            ));
    } else {
        http_response_code(401);
        echo json_encode(array('Error' => 'Login failed.'));
        exit;
    }
}


?>