<?php 
session_start();
include 'dbcon.php'; // Include your database connection

$response = array();

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch teachers assigned to the student, including their photo and status
    $stmt = $conn->prepare("SELECT t.firstname, t.lastname, t.location, t.teacher_status
                            FROM teacher t
                            JOIN class c ON t.class_id = c.class_id
                            JOIN student s ON c.class_id = s.class_id
                            WHERE s.student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = array(
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'location' => $row['location'], // Teacher photo location
            'teacher_status' => $row['teacher_status'] // Teacher status
        );
    }
    
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
