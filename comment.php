<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $department = trim($_POST['department']);
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);
    $date = date('Y-m-d H:i:s');

    if (empty($full_name) || empty($username) || empty($department) || empty($subject) || empty($message_text)) {
        $message = "<p class='error-msg'>All fields must be filled!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO comment (full_name, username, department, subject, message, date) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $full_name, $username, $department, $subject, $message_text, $date);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<p class='success-msg'>Comment submitted successfully!</p>";
            $stmt->close();
            $conn->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            $message = "<p class='error-msg'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

$conn->close();

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Form</title>
    <link rel="stylesheet" href="css/comment.css">
    <link rel="stylesheet" href="css/themes.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="theme-switcher">
    <section>
        <h2 class="heading">Send Your<span> comment!</span></h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="commentForm">
            <div class="input-box">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Access Username" required>
            </div>
            <div class="input-box">
                <input type="text" name="department" placeholder="Department" required>
                <input type="text" name="subject" placeholder="Message Subject" required>
            </div>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <div class="button-container">
                <input type="submit" value="Send Message" class="btn">
                <a href="index.php" class="logout-btn">Logout</a>
            </div>
            <div id="message-container"><?php echo $message; ?></div>
        </form>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageContainer = document.getElementById('message-container');
            if (messageContainer.innerHTML.trim() !== '') {
                setTimeout(() => {
                    messageContainer.style.opacity = '0';
                    setTimeout(() => {
                        messageContainer.style.display = 'none';
                    }, 500);
                }, 5000);
            }
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = 'theme-switcher ' + savedTheme;
            }
            if (messageContainer.innerHTML.includes('success')) {
                document.getElementById('commentForm').reset();
            }
        });
    </script>
</body>
</html>