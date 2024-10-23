<?php
session_start();
include 'dbcon.php'; // Your database connection file

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: Index.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['student_id'];
$first_name = $last_name = $profile_image = '';

// Fetch user details from the database
$stmt = $conn->prepare("SELECT firstname, lastname, location FROM student WHERE student_id = ?");
if ($stmt) {
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $first_name = htmlspecialchars($row['firstname']);
        $last_name = htmlspecialchars($row['lastname']);
        $profile_image = !empty($row['location']) ? htmlspecialchars($row['location']) : 'Images/default.png';
    } else {
        // Default values if no user data is found
        $first_name = 'Guest';
        $last_name = '';
        $profile_image = 'Images/default.png';
    }
    $stmt->close();
} else {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Subjects</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <link rel="stylesheet" href="CSS/student-bootstrap.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="CSS/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- SIDEBAR -->
<div id="pgside" class="drawer">
    <div id="pguser">
        <h3>Welcome, <?php echo $first_name . " " . $last_name; ?></h3>
        <div class="profile-picture">
            <img src="<?php echo $profile_image; ?>" alt="Profile Picture" id="profileImage">
        </div>

        <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="profile_image" style="display:none;" accept="image/*" required>
            <button type="button" id="uploadBtn">Upload</button>
            <input type="submit" id="submitBtn" style="display:none;">
        </form>
    </div>

    <a href="Studentpanel.php" class="sidebar-btn active"><i class="ico fa fa-users"></i><span class="txt">My Class</span></a>
    <a href="Studentnotif.php" class="sidebar-btn"><i class="ico fa fa-envelope"></i><span class="txt">Notifications</span></a>
    <a href="StudentMessages.php" class="sidebar-btn"><i class="ico fa fa-chalkboard-teacher"></i><span class="txt">Messages</span></a>
    <a href="Studentmodule.php" class="sidebar-btn"><i class="ico fa fa-folder-open"></i><span class="txt">Modules</span></a>
    <a href="Student_Assignments.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Assignments</span></a>
    <a href="Quizzessd.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Quizzes</span></a>
    <a href="#" id="signOutBtn" class="sidebar-btn"><i class="ico fa fa-sign-out-alt"></i><span class="txt">Sign Out</span></a>
</div>

    <!-- MAIN CONTENT -->
    <main id="pgmain">
        <nav id="navbar">
            <div class="logo">Alternative Learning System</div>
            <button id="menu-toggle"><i class="fa fa-bars"></i></button>
        </nav>

        <div class="student-dashboard">
        <div class="dashboard-card">
            <h2>My Teachers/Schoolyear</h2> <!-- will get the data of school year -->
            <div class="card-content1">
                <h3>My Teachers</h3>
                <div id="teachersList" class="card-container"></div> <!-- Display the teachers here -->
            </div>
            <div class="card-content2">
                <h3>My Classmates</h3>
                <div id="classmatesList" class="card-container"></div> <!-- Display the classmates here -->
            </div>
            <div class="card-content3">
                <h3>My Subjects</h3>
                <select id="subjectDropdown"></select> <!-- Dropdown for subjects -->
            </div>
        </div>
    </div>
</main>
<!-- Loading Spinner Overlay -->
<div id="loading" style="display: none;">
    <div class="spinner"></div>
    <p>Uploading your image...</p>
</div>

<!-- JavaScript -->
<script src="Javascript/script1.js"></script>
<script src="Javascript/student.js"></script>
</body>
</html>
