<?php
include_once '../config/database.php';
include_once '../config/headers.php';

class Update{ 
    
    private $conn;

    public function __construct(){
        $databaseService = new DBConnection();
        $this->conn = $databaseService->getConnection();
    }

    public function updateData($cardName, $cardImg, $cardSize, $cardId){
        $query = 'UPDATE cardsinfo SET name = :card_name, images = :card_image, size = :card_size WHERE id = :card_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_name', $cardName);
        $stmt->bindParam(':card_image', $cardImg);
        $stmt->bindParam(':card_size', $cardSize);
        $stmt->bindParam(':card_id', $cardId);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num <= 0){
            echo json_encode(array('Error' => 'Cannot updated card. The card does not exist.'));
            exit;
        }

        echo json_encode(array('Success' => 'Card updated successfully.'));
    }

    public function __destruct(){
        return $this->conn = null;
    }
}

?>