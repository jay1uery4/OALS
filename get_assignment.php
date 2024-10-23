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

// Build the query with a search condition if a search term is provided
if (!empty($searchTerm)) {
    $searchTerm = $conn->real_escape_string($searchTerm); // Prevent SQL injection
    echo "Searching for: " . htmlspecialchars($searchTerm) . "<br>"; // Debugging line
    $query = "SELECT `assignment_id`, `floc`, `fdatein`, `fdesc`, `teacher_id`, `class_id`, `fname` FROM `assignment` WHERE `fdesc` LIKE '%$searchTerm%' OR `floc` LIKE '%$searchTerm%' OR `fname` LIKE '%$searchTerm%'";
} else {
    $query = "SELECT `assignment_id`,   `floc`, `fdatein`, `fdesc`, `teacher_id`, `class_id`, `fname` FROM `assignment`";
}

$result = $conn->query($query);
if (!$result) {
    echo "Error executing query: " . $conn->error; // Output the error if the query fails
    exit(); // Stop execution
}

// Initialize the output variable
$output = "";

// Display the table headers
$output .= "<table>";
$output .= "<tr>
        <th>Select</th>
        <th>Assignment Description</th>
        <th>File</th>
        <th>Date and Time</th>
        <th>Uploaded By</th>
        <th>Class ID</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>";

// Check if any results are returned
if ($result->num_rows > 0) {
    // Loop through the results and display the assignment data
    while ($row = $result->fetch_assoc()) {
        $fileLocation = '../uploads/' . htmlspecialchars($row['floc']);
        $fileName = basename(htmlspecialchars($row['floc']));
        $minimizedFileName = minimizeFileName($fileName);

        $output .= "<tr>";
        $output .= '<td><input type="checkbox" class="student-checkbox" value="' . htmlspecialchars($row['assignment_id']) . '"></td>';
        $output .= "<td>" . htmlspecialchars($row['fdesc']) . "</td>";
        $output .= "<td><a href='$fileLocation' target='_blank' download>$minimizedFileName</a></td>";
        $output .= "<td>" . htmlspecialchars($row['fdatein']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['fname']) . "</td>"; 
        $output .= "<td>" . htmlspecialchars($row['class_id']) . "</td>";
        $output .= "<td><button class='btn-edit' data-id='" . htmlspecialchars($row['assignment_id']) . "'>Edit</button></td>";
        $output .= "<td><button class='btn-delete' data-id='" . htmlspecialchars($row['assignment_id']) . "'>Delete</button></td>";
        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='8'>No assignments found</td></tr>";
}
$output .= "</table>";

$conn->close();

// Output the table HTML
echo $output;
?>
