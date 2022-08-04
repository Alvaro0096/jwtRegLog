<?php
include_once './config/database.php';
include_once './config/headers.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$conn = null;
$email = '';
$password = '';

$databaseService = new DBConnection();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(array('Error' => 'Invalid REQUEST METHOD.'));
    exit;
} 

if(empty($email) || $email == ''){
    echo json_encode(array('Error' => 'An email is required.'));
    exit;
} else {
    //==============================
    // CHECK USER FROM users
    //==============================

    $query = 'SELECT user_email FROM users WHERE user_email = :email LIMIT 0,1';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num <= 0){
        echo json_encode(array('Error' => 'No result founds.'));
        exit;
    }

    $bytes = random_bytes(15);
    $code = bin2hex($bytes);

    //==============================
    // INSERT INTO resetpass
    //==============================

    $insertQuery = "INSERT INTO resetpass (user_email, user_code) VALUES ('$email', '$code')";
    $insertStmt = $conn->prepare($insertQuery);
    $insertNum = $insertStmt->rowCount();

    //==============================
    // CHECK REPEAT MAIL IN resetpass
    //==============================

    $checkQuery = "SELECT user_email FROM resetpass WHERE user_email = '$email'";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute();
    $count = $checkStmt->rowCount();

    if($count > 0){
        echo json_encode(array('Error' => 'Email already exist.'));
        exit;
    }

    if($insertStmt->execute()) {
        echo json_encode(array('Success' => 'User was successfully registered.'));
    }
}

$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'newTestMail0096@gmail.com';                     //SMTP username
    $mail->Password   = 'hgxenqtydyskcxta';                               //SMTP password
    $mail->SMTPSecure = 'tsl';            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('newTestMail0096@gmail.com', 'Mailer');
    $mail->addAddress('newTestMail0096@gmail.com', 'Mailer');     //Add a recipient

    //Content
    $url =  'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/updatePass.php?code=$code";
    $mail->isHTML(true);                                  
    $mail->Subject = 'Restore password link';
    $mail->Body    = "You have requested a password reset. 
                    Enter <a href='$url'>this link</a> to confirm";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo json_encode(array('Success' => 'Message has been sent'));
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>