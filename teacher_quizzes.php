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
    <link rel="stylesheet" href="CSS/quiz.css"> 
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
    <a href="teacher_assignments.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Assignments</span></a>
    <a href="teacher_Quizzes.php" class="sidebar-btn active"><i class="ico fa fa-file-alt"></i><span class="txt">Quizzes</span></a>
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
                <h2>My Quizzes</h2>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search..." />
                    <i class="fas fa-search"></i>
                    <button id="add-quiz-btn" class="btn-add-quiz">Add a Quiz</button>
                    <button id="assign-quiz-btn" class="btn-add-quiz">Assign Quiz to Class</button>
                    <button id="deleteSelectedBtn" class="btn-delete-all">Delete Selected</button>
                </div>
                
                <div class="subject-cards" id="subjectCards">
                    <!-- Quizzes will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Quiz Modal -->
<div id="addQuizModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add a Quiz</h2>
        <form id="addQuizForm">
            <label for="quiz_title">Quiz Title:</label>
            <input type="text" id="quiz_title" name="quiz_title" required>
            <label for="quiz_description">Quiz Description:</label>
            <textarea id="quiz_description" name="quiz_description" required></textarea>
            <button type="submit">Add Quiz</button>
        </form>
    </div>
</div>

<!-- Assign Quiz to Class Modal -->
<div id="assignQuizModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Assign Quiz to Class</h2>
        <form id="assignQuizForm" method="POST" action="assign_quiz.php">
            <label for="quiz_select">Select Quiz:</label>
            <select id="quiz_select" name="quiz_id" required>
                <!-- Quiz options will be populated here -->
            </select>

            <label for="test_time">Test Time (in minutes):</label>
            <input type="number" id="test_time" name="test_time" min="1" required>

            <h3>Classes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Grade Levels</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody id="classList">
                    <!-- Class list will be populated here with checkboxes -->
                </tbody>
            </table>

            <button type="submit">Assign Quiz</button>
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
<script src="Javascript/Teacher_quizzes.js"></script>
</body>
</html>
