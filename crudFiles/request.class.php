<?php
include_once '../config/database.php';
include_once '../config/headers.php';

class Request{ 
    
    private $conn;

    public function __construct(){
        $databaseService = new DBConnection();
        $this->conn = $databaseService->getConnection();
    }

    public function getCards(){
        $query = 'SELECT * FROM cardsinfo';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        if($num <= 0){
            echo json_encode(array('Error' => 'No results found.'));
        } 

        $newArr = [];
        $jsonRow;
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $rowArr = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'images' => $row['images'],
                'size' => $row['size'],
            );
            array_push($newArr, $rowArr);
            $jsonRow = json_encode($newArr, 128);
        }
        
        print_r($jsonRow);
    }

    public function __destruct(){
        return $this->conn = null;
    }
}

?>