<?php
session_start();
require 'dbcon.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    // Check whether the user is a student or a teacher
    if (isset($_SESSION['student_id'])) {
        $user_id = $_SESSION['student_id']; // Get student ID from session
        $user_type = 'student'; // To handle in the query later
    } elseif (isset($_SESSION['teacher_id'])) {
        $user_id = $_SESSION['teacher_id']; // Get teacher ID from session
        $user_type = 'teacher'; // To handle in the query later
    } else {
        echo json_encode(['success' => false, 'message' => "User session not found."]);
        exit();
    }

    $file = $_FILES['profile_image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => "File upload error."]);
        exit();
    }

    // Validate file type and size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => "Invalid file type. Only JPG, PNG, and GIF allowed."]);
        exit();
    }

    if ($file['size'] > 2 * 1024 * 1024) { // Limit to 2MB
        echo json_encode(['success' => false, 'message' => "File size exceeds the limit."]);
        exit();
    }

    // Generate a unique file name to avoid overwriting
    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = 'Images/' . $fileName;

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update the database with the new profile image path based on the user type
        if ($user_type === 'student') {
            $stmt = $conn->prepare("UPDATE student SET location = ? WHERE student_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE teacher SET location = ? WHERE teacher_id = ?");
        }

        $stmt->bind_param("ss", $uploadPath, $user_id);

        if ($stmt->execute()) {
            // Successful update
            echo json_encode(['success' => true, 'message' => "Profile image updated successfully.", 'imagePath' => $uploadPath]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => "Database update failed: " . $stmt->error]);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Failed to move uploaded file."]);
        exit();
    }
}
?>
