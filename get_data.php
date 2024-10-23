<?php
session_start();
header('Content-Type: application/json');
include 'dbcon.php'; // Assuming you have dbcon.php for database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT firstname, lastname, location FROM student WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstname = htmlspecialchars($row['firstname']);
    $lastname = htmlspecialchars($row['lastname']);
    $profile_image = !empty($row['location']) ? $row['location'] : 'Images/default.png';

    echo json_encode([
        'status' => 'success',
        'firstname' => $firstname,
        'lastname' => $lastname,
        'profile_image' => $profile_image
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
?>
