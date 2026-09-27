<?php 

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/Disaster.php"; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/disaster/create_disaster.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_disaster'])) {
    
    $db = new Database();
    $disasterModel = new Disaster($db->connect());
    
    $title         = trim($_POST['title'] ?? '');
    $disaster_type = $_POST['disaster_type'] ?? '';
    $description   = trim($_POST['description'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $district_id   = $_SESSION['district_id'] ?? '1'; 
    $priority      = $_POST['priority'] ?? '';
    
    if (!empty($title) && !empty($district_id)) {
        try {
            $disasterModel->create($title, $disaster_type, $description, $location, $district_id, $priority);

            $_SESSION['success_message'] = "Disaster reported successfully.";
            header("Location: ../views/disaster/manage_disasters.php?status=success");
            exit();

        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error saving assignment: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    }
}

header("Location: ../views/disaster/create_disaster.php");
exit();
?>
