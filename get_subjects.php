<?php
session_start();
include 'dbcon.php'; // Include your database connection

$response = array();

if (isset($_SESSION['class_id'])) {
    $class_id = $_SESSION['class_id'];

    // Fetch subjects for the student's class
    $stmt = $conn->prepare("SELECT subject_id, subject_title FROM subject WHERE class_id = ?");
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
    
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
