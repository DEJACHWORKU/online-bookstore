<?php
session_start();
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
                <input type="text" name="signin-username" id="signin-username" placeholder="Enter your username" value="<?php echo htmlspecialchars($signin_username); ?>">
                <div class="signin-error error-message"><?php echo $signin_username_error; ?></div>
            </div>
            <div class="form-group">
                <label for="signin-password">Password:</label>
                <input type="password" name="signin-password" id="signin-password" placeholder="Enter your password">
                <div class="signin-error error-message"><?php echo $signin_password_error; ?></div>
            </div>
            <button class="btn" type="submit" name="signin-submit">Login</button>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </form>
    </div>
</div>

<script>
function toggleForms() {
    const signupForm = document.getElementById('signup');
    const signinForm = document.getElementById('signin');
    if (signupForm.style.display === 'none') {
        signupForm.style.display = 'block';
        signinForm.style.display = 'none';
    } else {
        signupForm.style.display = 'none';
        signinForm.style.display = 'block';
    }
}

window.addEventListener('resize', function() {
    const formsContainer = document.querySelector('.container');
    if (window.innerWidth < 768) {
        formsContainer.style.flexDirection = 'column'; 
    } else {
        formsContainer.style.flexDirection = 'row'; 
    }
});

window.dispatchEvent(new Event('resize'));

document.getElementById('scroll-down').addEventListener('click', function() {
    window.scrollBy({
      top: window.innerHeight,
      behavior: 'smooth'
    });
});

window.addEventListener('scroll', function() {
    const scrollTopBtn = document.getElementById('scroll-top');
    if (window.pageYOffset > 300) {
      scrollTopBtn.classList.add('active');
    } else {
      scrollTopBtn.classList.remove('active');
    }
});

document.getElementById('scroll-top').addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
});
</script>
</body>
</html>