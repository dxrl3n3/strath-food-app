<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="registration-container">
        <form id="registration-form" class="registration-form" method="POST" action="process_register.php">
            <h2>Create Your Account</h2>
            
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Create a password (min 8 characters)">
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required placeholder="Confirm your password">
            </div>
            
            <div class="form-group">
                <label for="role">Account Type</label>
                <select id="role" name="role" required>
                    <option value="">Select your role</option>
                    <option value="student">Student (Place food orders)</option>
                    <option value="admin">Administrator (Manage system settings)</option>
                    <option value="kitchen">Kitchen Staff (Process food orders)</option>
                </select>
                <div class="role-description">
                    <p><strong>Student:</strong> Place orders from university cafeterias and track orders</p>
                    <p><strong>Administrator:</strong> Manage menus, users, and system configurations</p>
                    <p><strong>Kitchen Staff:</strong> Receive and prepare food orders from students</p>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Register Now</button>
            
            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>

    <script>
        // Client-side validation
        document.getElementById('registration-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const email = document.getElementById('email').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return;
            }
        });
    </script>
</body>
</html>