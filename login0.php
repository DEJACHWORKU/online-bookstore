<?php
session_start();

if (isset($_SESSION['logged_in']) && isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Librarian') {
        header("Location: librarian.php");
        exit();
    }
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
        $stmt = $conn->prepare("SELECT id, username, password, full_name, profile_image FROM Librarian WHERE username = ?");
        $stmt->bind_param("s", $signin_username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($signin_password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['role'] = 'Librarian';
                $_SESSION['logged_in'] = true;
                header("Location: librarian.php");
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
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
    <div id="signin" class="form">
        <h2>LIBRARIAN Login</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="signin-username">Username:</label>
                <input type="text" name="signin-username" id="signin-username" 
                       placeholder="Enter your username" 
                       value="<?php echo htmlspecialchars($signin_username); ?>" 
                       required>
                <div class="error-message"><?php echo $signin_username_error; ?></div>
            </div>
            <div class="form-group">
                <label for="signin-password">Password:</label>
                <input type="password" name="signin-password" id="signin-password" 
                       placeholder="Enter your password" required>
                <div class="error-message"><?php echo $signin_password_error; ?></div>
            </div>
            <button class="btn" type="submit" name="signin-submit">Login</button>
            <a href="forgot_adlab_pass.php" class="forgot-password">change/Forgot your Password?</a>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        let username = document.getElementById('signin-username').value;
        let password = document.getElementById('signin-password').value;
        let usernameError = document.querySelector('#signin-username + .error-message');
        let passwordError = document.querySelector('#signin-password + .error-message');
        let valid = true;

        if (!username.trim()) {
            usernameError.textContent = "Username is required.";
            fadeOutError(usernameError);
            valid = false;
        }

        if (!password.trim()) {
            passwordError.textContent = "Password is required.";
            fadeOutError(passwordError);
            valid = false;
        }

        return valid;
    }

    function fadeOutError(element) {
        setTimeout(() => {
            element.classList.add('fade-out');
            setTimeout(() => {
                element.textContent = "";
                element.classList.remove('fade-out');
            }, 500);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.error-message').forEach(error => {
            if (error.textContent) {
                fadeOutError(error);
            }
        });
    });
</script>
</body>
</html>