<?php
// Include the correct database connection
include('dbcon.php'); // Ensure the dbcon.php file has the $conn database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = $conn->real_escape_string(trim($_POST['username']));
    $fname = $conn->real_escape_string(trim($_POST['firstname']));
    $lname = $conn->real_escape_string(trim($_POST['lastname']));
    $class = $conn->real_escape_string(trim($_POST['class']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Passwords do not match. Please try again.');
                window.history.back();
              </script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the student ID already exists
    $checkStmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
    if (!$checkStmt) {
        die("Database query failed: " . $conn->error); // Check for errors in the query preparation
    }
    
    $checkStmt->bind_param("s", $student_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Student ID already exists
        echo "<script>
                alert('Student ID already exists. Please use a different ID.');
                window.history.back();
              </script>";
    } else {
        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO student (student_id, s_username, firstname, lastname, class_id, password) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Database query failed: " . $conn->error); // Check for errors in the query preparation
        }

        $stmt->bind_param("ssssss", $student_id, $username, $fname, $lname, $class, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>
                    alert('Registration successful!');
                    setTimeout(function() {
                        window.location.href = 'Index.php';
                    }, 1000);
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . addslashes($stmt->error) . "');
                    window.history.back();
                  </script>";
        }
    }

    // Close the statements
    $checkStmt->close();
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
