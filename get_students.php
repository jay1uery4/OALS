<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
include 'dbcon.php'; // Include database connection

// Check if class_id is provided from the request
if (isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']); // Convert to integer

    // Query to select students with the provided class_id
    $query = "SELECT student.*, class.class_name 
              FROM student 
              JOIN class ON student.class_id = class.class_id
              WHERE student.class_id = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $class_id); // Bind the class_id parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results are returned
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Profile Picture</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Grade Level</th>
                <th>Status</th>
              </tr>";

        // Loop through the results and display the student data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            // Construct the image path based on the location
            $imagePath = '../uploads/' . htmlspecialchars($row['location']); // Ensure correct path

            // Display the student photo
            echo "<td class='profile-picture'><img src='" . $imagePath . "' alt='Student photo'></td>";

            // Full name
            echo "<td>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</td>";
            
            // Username
            echo "<td>" . htmlspecialchars($row['s_username']) . "</td>";
            
            // Class Name (joined with class table)
            echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";

            // Status
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            
            echo "</tr>";
        }
        echo "</table>";
    } else {
        // No results found for this class
        echo "<p>No students found for this class.</p>";
    }
} else {
    // If no class_id is provided, return an error or empty state
    echo "<p>No class selected.</p>";
}

// Close the connection
$conn->close();
?>
