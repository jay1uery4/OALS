<?php
include 'dbcon.php'; // Include database connection

// Variables to store the result of the upload and the message to show
$upload_success = false;
$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $module_name = htmlspecialchars($_POST['module_name']);
    $class_id = intval($_POST['class_id']); // Ensure class_id is an integer
    $upload_date = date('Y-m-d H:i:s'); // Automatically set current date and time
    $uploaded_by = htmlspecialchars($_POST['uploaded_by']);

    // Define the directory where files will be stored
    $upload_dir = '../files/'; // Ensure this path is correct

    // Check if the directory exists, if not, create it
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $upload_success = false;
            $upload_message = 'Failed to create upload directory.';
        }
    }

    // Check if a file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Define the allowed file types
        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
        $file_name = $_FILES['file']['name'];
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

        // Check if the uploaded file has an allowed type
        if (in_array(strtolower($file_type), $allowed)) {
            // Define the temporary file path and destination file path
            $file_tmp_path = $_FILES['file']['tmp_name'];
            $file_dest_path = $upload_dir . basename($file_name);

            // **Check for duplicate files**
            if (file_exists($file_dest_path)) {
                $upload_success = false;
                $upload_message = 'File already exists.';
            } else {
                // Attempt to move the uploaded file to the server's file directory
                if (move_uploaded_file($file_tmp_path, $file_dest_path)) {
                    // Insert the module data into the database
                    $query = "INSERT INTO files (fname, floc, fdatein, fdesc, uploaded_by, class_id) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('sssssi', $module_name, $file_name, $upload_date, $module_name, $uploaded_by, $class_id);

                    // Check if the query executes successfully
                    if ($stmt->execute()) {
                        $upload_success = true;
                        $upload_message = 'Module uploaded successfully.';
                    } else {
                        $upload_success = false;
                        $upload_message = 'Failed to upload module. ' . $stmt->error;
                    }
                } else {
                    $upload_success = false;
                    $upload_message = 'Failed to move the uploaded file.';
                }
            }
        } else {
            $upload_success = false;
            $upload_message = 'Invalid file type. Only PDF, DOC, DOCX, PPT, PPTX, and TXT files are allowed.';
        }
    } else {
        $upload_success = false;
        $upload_message = 'No file uploaded or file upload error. Error code: ' . $_FILES['file']['error'];
    }
}

// Close the database connection
$conn->close();
?>