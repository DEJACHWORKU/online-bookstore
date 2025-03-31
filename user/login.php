<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userDB";
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
    } elseif (strlen($signin_username) < 6) {
        $signin_username_error = "Username must be at least 6 characters.";
    }
    if (empty($signin_password)) {
        $signin_password_error = "Password is required.";
    } elseif (strlen($signin_password) < 6) {
        $signin_password_error = "Password must be at least 6 characters.";
    }

    if (empty($signin_username_error) && empty($signin_password_error)) {
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $signin_username, $signin_password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['user_id'] = $signin_username;
            header("Location: index.php");
            exit();
        } else {
            $signin_password_error = "Incorrect username or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin Form</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
<div class="container">
    <div id="signin" class="form">
        <h2>Login Page</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="signin-username">Username:</label>
                <input type="text" name="signin-username" id="signin-username" placeholder="Enter your username" required value="<?php echo htmlspecialchars($signin_username); ?>">
                <div class="signin-error error-message"><?php echo $signin_username_error; ?></div>
            </div>
            <div class="form-group">
                <label for="signin-password">Password:</label>
                <input type="password" name="signin-password" id="signin-password" placeholder="Enter your password" required>
                <div class="signin-error error-message"><?php echo $signin_password_error; ?></div>
            </div>
            <button class="btn" type="submit" name="signin-submit">Login</button>
        </form>
        <?php if ($signin_password_error): ?>
            <div class="error-message"><?php echo $signin_password_error; ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>