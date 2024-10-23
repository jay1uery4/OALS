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
    <title>Teacher page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <link rel="stylesheet" href="CSS/Bootstrap-dashboard.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="CSS/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

    <a href="Teacherpanel.php" class="sidebar-btn active"><i class="ico fa fa-users"></i><span class="txt">My Class</span></a>
    <a href="teachernotif.php" class="sidebar-btn"><i class="ico fa fa-envelope"></i><span class="txt">Notifications</span></a>
    <a href="teacher_messages.php" class="sidebar-btn"><i class="ico fa fa-chalkboard-teacher"></i><span class="txt">Messages</span></a>
    <a href="teacher_module.php" class="sidebar-btn"><i class="ico fa fa-folder-open"></i><span class="txt">Modules</span></a>
    <a href="teacher_assignments.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Assignments</span></a>
    <a href="teacher_quizzes.php" class="sidebar-btn"><i class="ico fa fa-file-alt"></i><span class="txt">Quizzes</span></a>
    <a href="#" id="signOutBtn" class="sidebar-btn"><i class="ico fa fa-sign-out-alt"></i><span class="txt">Sign Out</span></a>
</div>

<!-- MAIN CONTENT -->
<main id="pgmain">
    <nav id="navbar">
        <div class="logo">Alternative Learning System</div>
        <button id="menu-toggle"><i class="fa fa-bars"></i></button>
    </nav>
    <div class="class-container">
        <!-- Class Headers -->
        <table class="t_table">
            <thead>
                <tr>
                    <!-- Pass class_id values when clicked -->
                    <th onclick="loadStudents(25)">BLP Basic Literacy Training</th>
                    <th onclick="loadStudents(26)">Elementary</th>
                    <th onclick="loadStudents(27)">Junior High School</th>
                    <th onclick="loadStudents(28)">Senior High School</th>
                </tr>
            </thead>
        </table>

        <!-- Container to Display Student Table -->
        <div id="students-table-container"></div>
    </div>
</main>

<!-- Loading Spinner Overlay -->
<div id="loading" style="display: none;">
    <div class="spinner"></div>
    <p>Uploading your image...</p>
</div>

<!-- JavaScript -->
<script src="Javascript/script1.js"></script>
<script>
    // Function to open the sidebar when the page loads
    window.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('pgside');
        const mainContent = document.getElementById('pgmain');
        
        // Add classes to ensure the sidebar is open by default
        sidebar.classList.add('open');
        mainContent.classList.add('active');
    });

    // Toggle sidebar visibility when menu toggle is clicked
    document.getElementById('menu-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('pgside');
        const mainContent = document.getElementById('pgmain');
        
        // Toggle the open class to hide/show the sidebar
        sidebar.classList.toggle('open');
        mainContent.classList.toggle('active');
    });

    // Sidebar button active state
    document.querySelectorAll('.sidebar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and add to the clicked one
            document.querySelectorAll('.sidebar-btn').forEach(button => {
                button.classList.remove('active');
            });
            this.classList.add('active');
            
            // Ensure the sidebar stays open after clicking the sidebar buttons
            const sidebar = document.getElementById('pgside');
            const mainContent = document.getElementById('pgmain');
            sidebar.classList.add('open');
            mainContent.classList.add('active');
        });
    });


    // Logout confirmation
    document.getElementById('signOutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to sign out?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, sign out!',
            cancelButtonText: 'No, stay logged in'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php'; // Redirect to logout script
            }
        });
    });

    // Upload profile picture functionality
    document.getElementById('uploadBtn').addEventListener('click', function() {
        const fileInput = document.getElementById('fileInput');
        fileInput.click();
    });

    document.getElementById('fileInput').addEventListener('change', function() {
        const file = this.files[0];
        const loading = document.getElementById('loading');

        if (file) {
            const reader = new FileReader();
            loading.style.display = 'flex'; // Show spinner

            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result; // Update the profile image
            };
            reader.readAsDataURL(file);
            
            // Submit the form automatically after file selection
            uploadProfileImage(); // Call the upload function
        }
    });

    // Function to handle the image upload
    function uploadProfileImage() {
        const formData = new FormData(document.getElementById('uploadForm'));
        const loading = document.getElementById('loading');
        loading.style.display = 'block'; // Show loading spinner

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none'; // Hide spinner
            if (data.success) {
                document.getElementById('profileImage').src = data.imagePath; // Update image source
                
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000 // Optional: Auto-close after 3 seconds
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            loading.style.display = 'none'; // Hide spinner on error
            Swal.fire({
                title: 'Error!',
                text: 'There was a problem uploading your image.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    // Prevent the user from going back to the login page using the back button
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };

    function loadStudents(classId) {
    console.log("class_id:", classId); // For debugging

    // AJAX request
    $.ajax({
        url: 'get_students.php',
        type: 'POST',
        data: { class_id: classId },
        success: function(response) {
            // Populate the container
            $('#students-table-container').html(response);
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Unable to load student data', 'error');
        }
    });

    // Remove 'active' class from all headers and add to the clicked one
    $('.table th').removeClass('active');
    event.target.classList.add('active'); // Add 'active' to the clicked header
}

    // Add the JavaScript function here
    document.querySelectorAll('.t_table th').forEach(th => {
        th.addEventListener('click', function() {
            // Remove 'active' class from all headers
            document.querySelectorAll('.t_table th').forEach(header => header.classList.remove('active'));
            
            // Add 'active' class to the clicked header
            this.classList.add('active');
        });
    });
</script>
</body>
</html>
