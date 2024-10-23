<?php
session_start();
include 'dbcon.php'; // Include your database connection

$response = array();

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch classmates in the same class
    $stmt = $conn->prepare("SELECT firstname, lastname 
                             FROM student 
                             WHERE class_id = (SELECT class_id FROM student WHERE student_id = ?) 
                             AND student_id != ?");
    $stmt->bind_param("ss", $student_id, $student_id);
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
