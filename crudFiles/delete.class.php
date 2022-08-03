<?php
include_once '../config/database.php';
include_once '../config/headers.php';

class Delete{ 
    
    private $conn;

    public function __construct(){
        $databaseService = new DBConnection();
        $this->conn = $databaseService->getConnection();
    }

    public function deleteData($cardId){
        $query = 'DELETE FROM cardsinfo WHERE id = :card_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_id', $cardId);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num <= 0){
            echo json_encode(array('Error' => 'Cannot delete card. The card does not exist.'));
            exit;
        }
        
        echo json_encode(array('Success' => 'Card deleted successfully.'));
    }

    public function __destruct(){
        return $this->conn = null;
    }
}

?>