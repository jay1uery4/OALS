<?php
// Include database connection
include 'dbcon.php';

// Prepare the SQL query to join class and subject tables based on subject_id
$sql = "SELECT class.class_id, class.class_name, subject.subject_title 
        FROM class 
        JOIN subject ON class.subject_id = subject.subject_id"; 

$result = $conn->query($sql);

// Initialize an array to store the results
$classes = [];

if ($result->num_rows > 0) {
    // Fetch the results into the array
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Return the data as JSON
echo json_encode($classes);

$conn->close();
?>
