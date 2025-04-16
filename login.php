<?php
session_start();

if (isset($_SESSION['logged_in'])) {
    header("Location: admin dashboard.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$signin_username_error = $signin_password_error = "";
$signin_username = $signin_password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin-submit'])) {
    $signin_username = trim($_POST['signin-username']);
    $signin_password = trim($_POST['signin-password']);
    
    if (empty($signin_username)) {
        $signin_username_error = "Username is required.";
    }
    if (empty($signin_password)) {
        $signin_password_error = "Password is required.";
    }
    
    if (empty($signin_username_error) && empty($signin_password_error)) {
        $sql = "SELECT id, username, password, full_name, profile_image FROM Admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $signin_username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($signin_password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['profile_image'] = $admin['profile_image'];
                $_SESSION['logged_in'] = true;
                header("Location: admin dashboard.php");
                exit();
            } else {
                $signin_password_error = "Invalid username or password.";
            }
        } else {
            $signin_username_error = "Invalid username or password.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
    <div id="signin" class="form">
        <h2>Admin Login</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="signin-username">Username:</label>
                <input type="text" name="signin-username" id="signin-username" 
                       placeholder="Enter admin username" 
                       value="<?php echo htmlspecialchars($signin_username); ?>" 
                       required>
                <div class="error-message"><?php echo $signin_username_error; ?></div>
            </div>
            <div class="form-group">
                <label for="signin-password">Password:</label>
                <input type="password" name="signin-password" id="signin-password" 
                       placeholder="Enter admin password" required>
                <div class="error-message"><?php echo $signin_password_error; ?></div>
            </div>
            <button class="btn" type="submit" name="signin-submit">Login</button>
            <a href="forgot_adlab_pass.php" class="forgot-password">change/Forgot your Password?</a>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', (e) => {
            let valid = true;
            const username = document.getElementById('signin-username').value.trim();
            const password = document.getElementById('signin-password').value.trim();
            const usernameError = document.querySelector('#signin-username + .error-message');
            const passwordError = document.querySelector('#signin-password + .error-message');
            
            usernameError.textContent = '';
            passwordError.textContent = '';
            
            if (!username) {
                usernameError.textContent = "Username is required.";
                valid = false;
            }
            
            if (!password) {
                passwordError.textContent = "Password is required.";
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
        
        document.querySelectorAll('.error-message').forEach(error => {
            if (error.textContent) {
                setTimeout(() => {
                    error.style.opacity = '0';
                    setTimeout(() => {
                        error.textContent = '';
                        error.style.opacity = '1';
                    }, 500);
                }, 5000);
            }
        });
    });
</script>
</body>
</html>