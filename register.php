<?php
include_once './config/database.php';
include_once './config/headers.php';

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

    echo 'Invalid REQUEST METHOD.';
    exit;

} 

if($email == '' || empty($email) || $password == '' || empty($password)){

    echo 'Email and Password must be completed.';
    exit;

} else {

    //=============================
    // INSERT USER
    //=============================

    $query = "INSERT INTO " . $table_name . "
    SET user_email = :email,
        user_pass = :password,
        user_type = :userType";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':userType', $userType);

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt->bindParam(':password', $password_hash);

    //=============================
    // CHECK REPEAT USER
    //=============================

    $checkQuery = "SELECT user_email FROM " . $table_name . " WHERE user_email = :email";

    $checkStmt = $conn->prepare($checkQuery);

    $checkStmt->bindParam(':email', $email);

    $checkStmt->execute();

    $count = $checkStmt->rowCount();

    if($count > 0){

    echo 'User already exist.';
    return false; 

    }

    if($email == '' || empty($email) || $password == '' || empty($password)){

    echo 'Email or password cannot be empty.';
    return false;

    } 

    if($stmt->execute()) {

    http_response_code(200);
    echo 'User was successfully registered.';

    } else {

    http_response_code(400);
    echo 'Unable to register the user.';
    }

}

?>