<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Form</title>
    <link rel="stylesheet" href="css/comment.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_Db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $message = ""; // Variable to store success/error message

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $full_name = $_POST['full_name'];
        $username = $_POST['username'];
        $department = $_POST['department'];
        $subject = $_POST['subject'];
        $message_text = $_POST['message'];
        $date = date('Y-m-d H:i:s'); // Current timestamp

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO comment (full_name, username, department, subject, message, date) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $full_name, $username, $department, $subject, $message_text, $date);

        // Execute and check
        if ($stmt->execute()) {
            $message = "<p class='success-msg'>Comment submitted successfully!</p>";
        } else {
            $message = "<p class='error-msg'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>

    <section>
        <h2 class="heading">Send Your<span> comment!</span></h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="input-box">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Access Username" required>
            </div>
            <div class="input-box">
                <input type="text" name="department" placeholder="Department" required>
                <input type="text" name="subject" placeholder="Email Subject" required>
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
        // Hide message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const messageContainer = document.getElementById('message-container');
            if (messageContainer.innerHTML.trim() !== '') {
                setTimeout(() => {
                    messageContainer.style.opacity = '0';
                    setTimeout(() => {
                        messageContainer.style.display = 'none';
                    }, 500); // Wait for fade-out animation
                }, 5000); // 5 seconds
            }
        });
    </script>
</body>
</html>