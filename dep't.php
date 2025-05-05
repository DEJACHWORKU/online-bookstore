<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = trim($_POST['dept_name']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name = ?");
    $stmt->bind_param("s", $department_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "<p class='error'>Department already exists!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        if ($stmt->execute()) {
            $message = "<p class='success'>New department added successfully!</p>";
        } else {
            $message = "<p class='error'>Error: " . $stmt->error . "</p>";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Department</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="stylesheet" href="css/add dep't.css">
</head>
<body>
   
    <div class="container">
        <h1 class="title">Add New Department</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="dept_name">Department Name</label>
                <input type="text" id="dept_name" name="dept_name" placeholder="Enter department name" required>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn">Add Department</button>
            </div>
        </form>
        <div class="message-container">
            <?php if (isset($message)) echo $message; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme initialization
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

            // Theme switcher toggle
            const settingsToggle = document.querySelector('.settings-btn');
            const themeOptions = document.querySelector('.theme-options');
            if (settingsToggle && themeOptions) {
                settingsToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
                });

                document.querySelectorAll('.theme-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const theme = this.getAttribute('data-theme');
                        document.body.className = theme;
                        localStorage.setItem('bookstoreTheme', theme);
                        themeOptions.style.display = 'none';
                    });
                });

                document.addEventListener('click', function(e) {
                    if (!settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                        themeOptions.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>