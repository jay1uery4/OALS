<?php
session_start();
include 'dbcon.php'; // Your database connection file

// Check if user is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Location: Index.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['teacher_id'];
$first_name = $last_name = $profile_image = '';

// Fetch user details from the database
$stmt = $conn->prepare("SELECT firstname, lastname, location FROM teacher WHERE teacher_id = ?");
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
    <title>Teacher Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <link rel="stylesheet" href="CSS/Bootstrap-dashboard.css"> 
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

    <a href="Teacherpanel.php" class="sidebar-btn"><i class="ico fa fa-users"></i><span class="txt">My Class</span></a>
    <a href="teachernotif.php" class="sidebar-btn"><i class="ico fa fa-envelope"></i><span class="txt">Notifications</span></a>
    <a href="teacher_messages.php" class="sidebar-btn"><i class="ico fa fa-chalkboard-teacher"></i><span class="txt">Messages</span></a>
    <a href="teacher_module.php" class="sidebar-btn"><i class="ico fa fa-folder-open"></i><span class="txt">Modules</span></a>
    <a href="teacher_assignments.php" class="sidebar-btn active"><i class="ico fa fa-file-alt"></i><span class="txt">Assignments</span></a>
    <a href="teacher_quizzes.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Quizzes</span></a>
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
            <div class="card-content">
                <h2>My Assignments</h2>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search..." />
                    <i class="fas fa-search"></i>
                    <button id="add-module-btn" class="btn-add-module">Add Assignment</button>
                    <button id="deleteSelectedBtn" class="btn-delete-all">Delete Selected</button>
                </div>
                
                <div class="subject-cards" id="subjectCards">
                    <!-- Modules will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Form for Uploading an Assignment -->
<div id="uploadAssignmentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Upload Assignment</h3>
        <form id="uploadAssignmentForm" action="upload_assignment.php" method="post" enctype="multipart/form-data">
            <label for="assignment_name">Assignment Name:</label>
            <input type="text" name="assignment_name" id="assignment_name" required>

            <label for="class_id">Select Class:</label>
            <select name="class_id" id="class_id" required>
                <option value="25">BLP Basic Literacy Training</option>
                <option value="26">Elementary</option>
                <option value="27">Junior High School</option>
                <option value="28">Senior High School</option>
            </select>

            <label for="uploaded_by">Uploaded By:</label>
            <input type="text" name="uploaded_by" id="uploaded_by" required>

            <label for="file">Choose File:</label>
            <input type="file" name="file" id="file" required>

            <button type="submit">Upload</button>
        </form>
    </div>
</div>

<!-- Loading Spinner Overlay -->
<div id="loading" style="display: none;">
    <div class="spinner"></div>
    <p>Uploading your image...</p>
</div>

<!-- JavaScript -->
<script src="Javascript/script1.js"></script>
<script src="Javascript/Teacher_assignment.js"></script>
</body>
</html>
