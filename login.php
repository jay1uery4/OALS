<?php
include('dbcon.php');
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $conn->real_escape_string(trim($_POST['username']));
        $password = trim($_POST['password']);

        // Debugging: Log the username and password
        error_log("Username: $username"); // Log the username

        // Function to handle login for both students and teachers
        function loginUser($conn, $username, $password, $role) {
            // Prepare the query and set redirect URL
            $query = $role == 'student' ? 
                "SELECT student_id, password FROM student WHERE s_username = ?" : 
                "SELECT teacher_id, password FROM teacher WHERE t_username = ?";
            $redirectUrl = $role == 'student' ? 'Studentpanel.php' : 'Teacherpanel.php';

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                error_log("Statement preparation failed: " . $conn->error); // Log SQL error
                echo "<script>
                        alert('Database error: Failed to execute query.');
                        window.location.href = 'Index.php';
                      </script>";
                exit();
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            // Check if a user was found
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id, $hashed_password);
                $stmt->fetch();

                // Debugging: Log the retrieved user ID and hashed password
                error_log("User ID: $user_id"); // Log the user ID

                // Check if the password is correct
                if (password_verify($password, $hashed_password)) {
                    // Set session variable based on the role
                    $_SESSION[$role . '_id'] = $user_id;
                    echo "<script>
                            alert('Login successful! Redirecting to your panel...');
                            setTimeout(function() {
                                window.location.href = '$redirectUrl';
                            }, 1500);
                          </script>";
                    exit();
                } else {
                    error_log("Password verification failed for user: $username"); // Log failed password verification
                }
            } else {
                error_log("No user found for username: $username"); // Log if no user was found
            }

            return false; // User not found or password incorrect
        }

        // Attempt to log in as a student or teacher
        if (loginUser($conn, $username, $password, 'student') || loginUser($conn, $username, $password, 'teacher')) {
            exit();
        }

        // No user found in both tables or incorrect password
        echo "<script>
                alert('Login failed: Incorrect username or password. Please try again.');
                window.location.href = 'Index.php';
              </script>";
    } else {
        // Username or password not set
        echo "<script>
                alert('Login failed: Please enter both username and password.');
                window.location.href = 'Index.php';
              </script>";
    }
}

// Close the connection after all operations are done
$conn->close();
?>
