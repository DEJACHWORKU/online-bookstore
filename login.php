<?php
session_start();

$signin_email_error = $signin_password_error = "";
$signin_success_message = "";

$signin_email = $signin_password = "";

// Handle signin form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin-submit'])) {
    $signin_email = trim($_POST['signin-email']);
    $signin_password = trim($_POST['signin-password']);

    // Validate email
    if (empty($signin_email) || !filter_var($signin_email, FILTER_VALIDATE_EMAIL)) {
        $signin_email_error = "Invalid email format.";
    }

    // Validate password
    if (empty($signin_password)) {
        $signin_password_error = "Password is required.";
    }

    // If no errors, proceed with signin
    if (empty($signin_email_error) && empty($signin_password_error)) {
        // Simulate a user check (replace this with actual user verification logic)
        if ($signin_email === "user@example.com" && $signin_password === "password123") {
            $signin_success_message = "Signin successful! Welcome back.";
            header("Location: book/index.php"); 
        } else {
            $signin_password_error = "Incorrect email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin Form</title>
    <link rel="stylesheet" href="css/login.css">
    <script src="css/script.js"></script>
</head>
<body>

<div class="container">

    <div id="signin" class="form">
        <h2>Login Page</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="signin-email">username:</label>
                <input type="email" name="signin-email" id="signin-email" placeholder="Enter your username" required value="<?php echo htmlspecialchars($signin_email); ?>">
                <div class="signin-error error-message"><?php echo $signin_email_error; ?></div>
            </div>
            <div class="form-group">
                <label for="signin-password">Password:</label>
                <input type="password" name="signin-password" id="signin-password" placeholder="Enter your password" required>
                <div class="signin-error error-message"><?php echo $signin_password_error; ?></div>
            </div>
            <button class="btn" type="submit" name="signin-submit">Login</button>

            <?php if ($signin_success_message): ?>
                <div class="success-message"><?php echo $signin_success_message; ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>