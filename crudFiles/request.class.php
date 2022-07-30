<?php
include_once '../config/database.php';
include_once '../config/headers.php';

class Request{ 
    
    private $conn;

    public function __construct(){

        $databaseService = new DBConnection();
        $this->conn = $databaseService->getConnection();

    }

    public function getData(){
        
        $query = 'SELECT * FROM userscard';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        if($num <= 0){
            echo 'No results found.';
        } 
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            foreach($row as $key => $value){
                echo $key .':'. $value . '<br>';
            }
        }

    }

    public function __destruct(){

        return $this->conn = null;

    }
}

?>