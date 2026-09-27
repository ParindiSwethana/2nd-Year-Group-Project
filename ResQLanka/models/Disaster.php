<?php

require_once __DIR__ . "/../config/database.php";

class Disaster {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function create($title, $type, $description, $location, $district_id, $priority) {
        $stmt = $this->conn->prepare("
            INSERT INTO disaster (title, disaster_type, description, location, district_id, priority) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([$title, $type, $description, $location, $district_id, $priority]);
    }
}
?>
