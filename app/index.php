<?php
session_start();
// Error reporting for debugging
ini_set('display_errors', 0); // Disable error display
ini_set('display_startup_errors', 0);
error_reporting(0); // Suppress error reporting

// Database configuration
$host = 'sql58.jnb2.host-h.net'; // Database host
$db = 'fi1g4_c2gww';             // Database name
$user = 'ef79e_iv2a7';           // Database username
$pass = 'V53O1j771Sy860';        // Database password
$charset = 'utf8mb4';

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Optionally log the error instead of displaying it
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed.");
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // User registration
        $new_username = $_POST['new_username'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];

        if (!empty($new_username) && !empty($new_password) && !empty($email) && !empty($contact_number)) {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->execute([$new_username]);

            if ($stmt->rowCount() == 0) {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, contact_number) VALUES (?, ?, ?, ?)");
                $stmt->execute([$new_username, $new_password, $email, $contact_number]);
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Username already exists.";
            }
        } else {
            $error = "All fields are required.";
        }
    } else {
        // User login
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            $_SESSION['user_name'] = $username; // Optionally store username
            header('Location: /taskmanager/app/models/UserDashboard.php'); // Redirect to user dashboard
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// Include the User model
include 'models/User.php';
$userModel = new User($pdo); // Create an instance of User model

// Get the active user if logged in
$activeUser = null;
if (isset($_SESSION['user_id'])) {
    $activeUser = $userModel->getActiveUser($_SESSION['user_id']);
}

// HTML content for login and registration
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('./images/Recruitment-back.jpg'); /* Replace with your background image URL */
            background-size: cover; /* Ensures the background image covers the entire page */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-position: center; /* Centers the image */
            height: 100vh; /* Ensures full height coverage */
            display: flex; /* Centers the login container */
            align-items: center; /* Vertically centers */
            justify-content: center; /* Horizontally centers */
        }
        .login-container {
            max-width: 400px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9); /* Adds a slight transparency to the container */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="./images/Recruitment-Logo-Lance.png" alt="Company Logo" class="logo">
        <h2>Login</h2>
        <ul class="nav nav-tabs" id="loginTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="user-tab" data-toggle="tab" href="#user" role="tab">User Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab">Create Account</a>
            </li>
        </ul>
        <div class="tab-content" id="loginTabContent">
            <div class="tab-pane fade show active" id="user" role="tabpanel">
                <form method="POST" class="mt-3">
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
            <div class="tab-pane fade" id="register" role="tabpanel">
                <form method="POST" class="mt-3">
                    <div class="form-group">
                        <input type="text" name="new_username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="new_password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary btn-block">Create Account</button>
                </form>
            </div>
        </div>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
