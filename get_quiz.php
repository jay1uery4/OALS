<?php
include 'dbcon.php'; // Include database connection

// Function to minimize file name with ellipsis
function minimizeFileName($filename, $maxLength = 20) {
    if (strlen($filename) > $maxLength) {
        $start = substr($filename, 0, ceil($maxLength / 2));
        $end = substr($filename, -ceil($maxLength / 2));
        return $start . '...' . $end;
    }
    return $filename;
}

// Check if a search term is provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query for quizzes
$query = "SELECT q.quiz_id, q.quiz_title, q.quiz_description, q.date_added, qt.question_type
          FROM quiz q
          LEFT JOIN quiz_question qq ON q.quiz_id = qq.quiz_id
          LEFT JOIN question_type qt ON qq.question_type_id = qt.question_type_id
          WHERE q.quiz_title LIKE '%$searchTerm%' OR q.quiz_description LIKE '%$searchTerm%' 
          GROUP BY q.quiz_id";

$result = $conn->query($query);

// Initialize the output variable
$output = "";

// Display the table headers
$output .= "<table>";
$output .= "<tr>
        <th>Select</th>
        <th>Quiz Title</th>
        <th>Description</th>
        <th>Date Added</th>
        <th>Question Type</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>";

// Check if any results are returned
if ($result->num_rows > 0) {
    // Loop through the results and display the quiz data
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>";
        $output .= '<td><input type="checkbox" class="quiz-checkbox" value="' . htmlspecialchars($row['quiz_id']) . '"></td>';
        $output .= "<td>" . htmlspecialchars($row['quiz_title']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['quiz_description']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['date_added']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['question_type']) . "</td>";
        $output .= "<td><button class='btn-edit' data-id='" . htmlspecialchars($row['quiz_id']) . "'>Edit</button></td>";
        $output .= "<td><button class='btn-delete' data-id='" . htmlspecialchars($row['quiz_id']) . "'>Delete</button></td>";
        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='7'>No quizzes found</td></tr>";
}
$output .= "</table>";

$conn->close();

// Output the table HTML
echo $output;
?>
