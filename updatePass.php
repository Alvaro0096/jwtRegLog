<?php
include_once './config/database.php';
include_once './config/headers.php';

$password = '';
$conn = null;

$databaseService = new DBConnection();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$password = $data->password;
$password_hash = password_hash($password, PASSWORD_BCRYPT);

$code = $_GET['code'];

$query = "SELECT * FROM resetpass WHERE user_code = '$code'";
$stmt = $conn->prepare($query); 
$stmt->execute();
$num = $stmt->rowCount();

if($num <= 0){
    echo json_encode(array('Error' => 'No result founds.'));
    exit;
}

if(empty($password) || $password == ''){
    echo json_encode(array('Error' => 'A password is required.'));
    exit;
} else {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

$id = $row['user_id'];
$email = $row['user_email'];

$updateQuery = "UPDATE users SET user_pass = '$password_hash' WHERE user_email = '$email'";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->execute();
$num = $updateStmt->rowCount();

if($num <= 0){
    echo json_encode(array('Error' => 'Cannot updated password. The user does not exist.'));
    exit;
} else {
    $deleteQuery = "DELETE FROM resetpass WHERE user_code = '$code'";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->execute();
    echo json_encode(array('Success' => 'The password has been updated.'));
}

?>