<?php
session_start(); // Start the session

// Redirect if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirect to the appropriate panel based on user role
    if (isset($_SESSION['student_id'])) {
        header("Location: Studentpanel.php");
    } elseif (isset($_SESSION['teacher_id'])) {
        header("Location: Teacherpanel.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Online Alternative Learning System</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar Section -->
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <img src="Images/ALS.png" alt="Logo" class="logo">
                <div class="motto">Life Long Learning</div>
            </div>
            <ul class="nav-menu">
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i> Contacts</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Feedback</a></li>
            </ul>
            <div class="menu-icon" onclick="toggleSidePanel()">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
        <div class="side-panel">
            <span class="close-btn" onclick="toggleSidePanel()">✖</span>
            <ul class="nav-menu">
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i> Contacts</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Feedback</a></li>
            </ul>
        </div>
    </header>

    <!-- Login Form -->
    <div class="wrapper">
        <header>Login Form</header>
        <form action="login.php" method="POST">
            <!-- ID Field -->
            <div class="field login_id">
                <div class="input-area">
                    <input type="text" name="username" placeholder="User ID" required>
                    <i class="icon fas fa-user"></i>
                    <i class="error error-icon fas fa-exclamation-circle"></i>
                </div>
                <div class="error error-txt">ID can't be blank</div>
            </div>

            <!-- Password Field -->
            <div class="field password">
                <div class="input-area">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="icon fas fa-lock"></i>
                    <i class="error error-icon fas fa-exclamation-circle"></i>
                </div>
                <div class="error error-txt">Password can't be blank</div>
            </div>

            <div class="pass-txt"><a href="#">Forgot password?</a></div>
            <input type="submit" value="Login">
        </form>
        <div class="sign-txt">Not yet a member? <a href="Studentform.php">Signup now</a></div>
    </div>

    <script>
        const menuIcon = document.querySelector('.menu-icon');
        const sidePanel = document.querySelector('.side-panel');

        function toggleSidePanel() {
            if (window.innerWidth < 769) {
                sidePanel.classList.toggle('active');
                menuIcon.style.display = sidePanel.classList.contains('active') ? 'none' : 'flex';
            }
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 769) {
                sidePanel.classList.remove('active');
                menuIcon.style.display = 'none';
            } else {
                menuIcon.style.display = 'flex';
            }
        });
    </script>
</body>
</html>
