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
// After fetching the search term
if (!empty($searchTerm)) {
    $searchTerm = $conn->real_escape_string($searchTerm); // Prevent SQL injection
    echo "Searching for: " . $searchTerm; // Debugging line
    $query = "SELECT * FROM `files` WHERE `fdesc` LIKE '%$searchTerm%' OR `floc` LIKE '%$searchTerm%' OR `uploaded_by` LIKE '%$searchTerm%'";
} else {
    $query = "SELECT * FROM `files`";
}


$result = $conn->query($query);

// Initialize the output variable
$output = "";

// Display the table headers
$output .= "<table>";
$output .= "<tr>
        <th>Select</th>
        <th>Module Description</th>
        <th>File</th>
        <th>Date and Time</th>
        <th>Uploaded By</th>
        <th>Class ID</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>";

// Check if any results are returned
if ($result->num_rows > 0) {
    // Loop through the results and display the module data
    while ($row = $result->fetch_assoc()) {
        $fileLocation = '../uploads/' . htmlspecialchars($row['floc']);
        $fileName = basename(htmlspecialchars($row['floc']));
        $minimizedFileName = minimizeFileName($fileName);

        $output .= "<tr>";
        $output .= '<td><input type="checkbox" class="student-checkbox" value="' . htmlspecialchars($row['file_id']) . '"></td>';
        $output .= "<td>" . htmlspecialchars($row['fdesc']) . "</td>";
        $output .= "<td><a href='$fileLocation' target='_blank' download>$minimizedFileName</a></td>";
        $output .= "<td>" . htmlspecialchars($row['fdatein']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['uploaded_by']) . "</td>";
        $output .= "<td>" . htmlspecialchars($row['class_id']) . "</td>";
        $output .= "<td><button class='btn-edit' data-id='" . htmlspecialchars($row['file_id']) . "'>Edit</button></td>";
        $output .= "<td><button class='btn-delete' data-id='" . htmlspecialchars($row['file_id']) . "'>Delete</button></td>";
        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='8'>No modules found</td></tr>";
}
$output .= "</table>";

$conn->close();

// Output the table HTML
echo $output;
?>
