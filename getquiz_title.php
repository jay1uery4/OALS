<?php
// Include database connection
include 'dbcon.php';

header('Content-Type: application/json');

try {
    // Fetch quizzes from the database
    $sql = "SELECT quiz_id, quiz_title FROM quiz";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all quizzes
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return quizzes as JSON
    echo json_encode($quizzes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
