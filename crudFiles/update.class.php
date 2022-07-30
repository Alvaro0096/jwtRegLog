<?php
include_once '../config/database.php';
include_once '../config/headers.php';

class Update{ 
    
    private $conn;

    public function __construct(){

        $databaseService = new DBConnection();
        $this->conn = $databaseService->getConnection();

    }

    public function updateData($email, $password, $userType){

        $query = 'UPDATE users SET user_email = :user_email, user_pass = :user_pass, user_type = :user_type WHERE user_id = 10';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_email', $email);
        $stmt->bindParam(':user_pass', $password);
        $stmt->bindParam(':user_type', $userType);

        if($stmt->execute()){
            echo 'User updated successfully.';
        } else {
            echo 'Cannot updated user.';
        }
        
    }

    public function __destruct(){

        return $this->conn = null;

    }
}

?>