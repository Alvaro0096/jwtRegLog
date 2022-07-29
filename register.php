<?php
include_once './config/database.php';
include_once './config/headers.php';

$email = '';
$password = '';
$userType = '';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$userType = $data->userType;

$table_name = 'users';

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


if($stmt->execute()){

    http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
}
else{
    http_response_code(400);

    echo json_encode(array("message" => "Unable to register the user."));
}
?>