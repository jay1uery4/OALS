<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration Form</title>
  <link rel="stylesheet" href="CSS/signup.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
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

    <!-- Main Content -->
    <div class="wrapper">
        <h2>Student Registration Form</h2>
        <form action="student_signup.php" method="POST" id="registrationForm">  
            <div class="form-grid">
                <div class="field id">
                    <div class="input-area">
                        <input type="text" name="username" id="username" placeholder="Student ID" required>
                        <i class="icon fas fa-user"></i>
                    </div>
                    <div class="error error-txt">Student ID can't be blank</div>
                </div>

                <div class="field fname">
                    <div class="input-area">
                        <input type="text" name="firstname" id="firstname" placeholder="Firstname" required>
                        <i class="icon fas fa-user"></i>
                    </div>
                    <div class="error error-txt">Firstname can't be blank</div>
                </div>

                <div class="field lname">
                    <div class="input-area">
                        <input type="text" name="lastname" id="lastname" placeholder="Lastname" required>
                        <i class="icon fas fa-user"></i>
                    </div>
                    <div class="error error-txt">Lastname can't be blank</div>
                </div>

                <div class="field class">
                    <div class="input-area">
                        <select name="class" id="class" required>
                            <option value="" disabled selected>Select level</option>
                            <?php
                            include('dbcon.php');
                            
                            // Checking for connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }
                            
                            $query = "SELECT class_id, class_name FROM class";  
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                // If the query fails, display an error
                                echo "Error executing query: " . mysqli_error($conn);
                            } else {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="error error-txt">Class can't be blank</div>
                </div>

                <div class="field password">
                    <div class="input-area">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <i class="icon fas fa-lock"></i>
                        <i class="error error-icon fas fa-exclamation-circle"></i>
                    </div>
                    <div class="error error-txt" id="password-error">Password can't be blank</div>
                </div>

                <div class="field password">
                    <div class="input-area">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                        <i class="icon fas fa-lock"></i>
                        <i class="error error-icon fas fa-exclamation-circle"></i>
                    </div>
                    <div class="error error-txt" id="confirm_password-error">Confirm Password can't be blank</div>
                </div>
            </div>
            <input type="submit" value="Signup">
        </form>
        <div class="sign-txt">Already a member? <a href="Index.php">Signin now</a></div>
    </div>

    <!-- Sidebar Toggle Script -->
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
    
    <!-- Additional Scripts -->
    <script src="Javascript/script.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>
