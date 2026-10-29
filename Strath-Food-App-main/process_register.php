<?php
include "header.php";
// Include database connection
require_once "connection.php";

// Initialize message variables
$message = "";
$messageType = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $fullname = trim(filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    
    // Initialize errors array
    $errors = [];
        // Admin email restriction
    if ($role === "admin" && !str_ends_with(strtolower($email), "@strathmore.edu")) {
        $errors[] = "Only Strathmore emails can register as Admin.";
    }

    if ($role === "kitchen" && !str_ends_with(strtolower($email), "@strathmore.edu")) {
        $errors[] = "Only Strathmore emails can register as Staff.";
    }
    // Validate inputs
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    // Check password length
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // Validate role
    if (!in_array($role, ['student', 'admin', 'kitchen'])) {
        $errors[] = "Invalid role selected";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Email already registered. Please use a different email.";
            $messageType = "error";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare and execute SQL statement
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $role);
            
            if ($stmt->execute()) {
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
                $messageType = "success";
            } else {
                $message = "Registration failed: " . $stmt->error;
                $messageType = "error";
            }
            
            $stmt->close();
        }
        $checkEmail->close();
    } else {
        // Display errors
        $message = implode("<br>", array_map('htmlspecialchars', $errors));
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="registration.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="registration-container">
        <div class="registration-form">
            <h2>Registration Status</h2>
            
            <?php if (!empty($message)): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="registration.php" class="cta-button">Back to Registration</a>
                <a href="index.php" class="cta-button secondary">Go to Home</a>
            </div>
        </div>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>

    <script>
        // Auto redirect after successful registration
        <?php if ($messageType === "success"): ?>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 10000);
        <?php endif; ?>
    </script>
</body>
</html>