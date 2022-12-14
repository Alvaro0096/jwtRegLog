<?php
include_once './config/database.php';
include_once './config/headers.php';

$email = '';
$password = '';
$conn = null;

$databaseService = new DBConnection();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$confirmPassword = $data->confirmPassword;

$table_name = 'users';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

if($email == '' || empty($email) || $password == '' || empty($password)){
    echo json_encode(array('Error' => 'Email, password and user type must be completed.'));
    exit;
} else {
    //=============================
    // INSERT USER
    //=============================

    $query = "INSERT INTO " . $table_name . "
    SET user_email = :email,
        user_pass = :password";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if($password === $confirmPassword){
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    } else {
        echo json_encode(array('Error' => 'The passwords do not match.'));
        exit;
    } 
    
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
        echo json_encode(array('Error' => 'User already exist.'));
        return false; 
    }

    if($email == '' || empty($email) || $password == '' || empty($password)){
        echo json_encode(array('Error' => 'Email, password and user type cannot be empty.'));
        return false;
    } 

    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array('Success' => 'User was successfully registered.'));
    } else {
        http_response_code(400);
        echo json_encode(array('Error' => 'Unable to register the user.'));
    }
}

?>