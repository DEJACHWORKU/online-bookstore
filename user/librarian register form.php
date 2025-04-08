<?php
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '21M');
ini_set('max_execution_time', '300');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = '';
    
    $fullName = trim($_POST['fullName'] ?? '');
    $personalID = trim($_POST['personalID'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $rememberMe = trim($_POST['rememberMe'] ?? '');
    $profileImage = $_FILES['profileImage'] ?? null;

    if (!preg_match("/^[a-zA-Z ]*$/", $fullName) || empty($fullName)) {
        $message .= "<p class='error'>Full Name must contain only letters and cannot be empty.</p>";
    }
    if (empty($personalID)) {
        $message .= "<p class='error'>Personal ID is required.</p>";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email)) {
        $message .= "<p class='error'>Valid email is required.</p>";
    }
    if (!preg_match("/^[0-9]*$/", $phone) || empty($phone)) {
        $message .= "<p class='error'>Phone number must contain only digits and cannot be empty.</p>";
    }
    if (empty($username)) {
        $message .= "<p class='error'>Username is required.</p>";
    }
    if (strlen($password) < 6 || empty($password)) {
        $message .= "<p class='error'>Password must be at least 6 characters long and is required.</p>";
    }
    if ($password !== $confirmPassword) {
        $message .= "<p class='error'>Passwords do not match.</p>";
    }

    if ($profileImage && $profileImage['error'] !== UPLOAD_ERR_OK && $profileImage['error'] !== UPLOAD_ERR_NO_FILE) {
        $message .= "<p class='error'>Error uploading profile image.</p>";
    } elseif ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
        $image_ext = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
        if (!in_array($image_ext, ['png', 'jpg', 'jpeg'])) {
            $message .= "<p class='error'>Profile image must be PNG, JPG, or JPEG.</p>";
        }
        if ($profileImage['size'] > 20971520) {
            $message .= "<p class='error'>Profile image must be less than 20MB.</p>";
        }
    }

    if (empty($message)) {
        $check_stmt = $conn->prepare("SELECT id FROM Librarian WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $message .= "<p class='error'>Username or email already exists.</p>";
        }
        $check_stmt->close();
    }

    if (empty($message)) {
        $profileImagePath = "";
        if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
            $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/Librarian/';
            $image_dir = $base_dir . 'uploads/profile_images/';
            $image_web_path = 'uploads/profile_images/';
            
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true);
            }

            $image_filename = uniqid() . '.' . $image_ext;
            $image_path = $image_web_path . $image_filename;

            if (move_uploaded_file($profileImage['tmp_name'], $image_dir . $image_filename)) {
                $profileImagePath = $image_path;
            } else {
                $message .= "<p class='error'>Error uploading profile image! Please check your file permissions.</p>";
            }
        }

        if (empty($message)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Librarian (full_name, personal_id, email, phone, username, password, profile_image, remember_me) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullName, $personalID, $email, $phone, $username, $hashedPassword, $profileImagePath, $rememberMe);
            
            if ($stmt->execute()) {
                $message = "<p class='success'>Librarian registration successful!</p>";
            } else {
                $message = "<p class='error'>Error: " . $stmt->error . "</p>";
                if ($profileImagePath && file_exists($image_dir . $image_filename)) {
                    unlink($image_dir . $image_filename);
                }
            }
            $stmt->close();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['message' => $message]);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/admin register.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Librarian Registration Form</h1>
        
        <form id="registrationForm" method="POST" enctype="multipart/form-data">
            <label for="profileImage">Profile Image (PNG/JPG, max 20MB, Optional):</label>
            <input type="file" id="profileImage" name="profileImage" accept="image/png, image/jpeg, image/jpg" data-max-size="20971520">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                    <i class="fas fa-user input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="personalID">Personal ID</label>
                    <input type="text" id="personalID" name="personalID" required>
                    <i class="fas fa-id-card input-icon"></i>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                    <i class="fas fa-phone input-icon"></i>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <i class="fas fa-user-tag input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="password-toggle" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <label for="rememberMe">Remember Me</label>
                    <input type="text" id="rememberMe" name="rememberMe" placeholder="Enter preference">
                    <i class="fas fa-check-circle input-icon"></i>
                </div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn">Register</button>
            </div>
            
            <div class="message-container" id="message-container"></div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.querySelector('#password + .password-toggle i');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        
        function toggleConfirmPassword() {
            const confirmPassword = document.getElementById('confirmPassword');
            const icon = document.querySelector('#confirmPassword + .password-toggle i');
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                confirmPassword.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const profileImage = document.getElementById('profileImage');
            const maxSize = 20971520;

            if (profileImage.files[0] && profileImage.files[0].size > maxSize) {
                document.getElementById('message-container').innerHTML = "<p class='error'>Profile image must be less than 20MB</p>";
                return;
            }

            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageContainer = document.getElementById('message-container');
                messageContainer.innerHTML = data.message;
                button.disabled = false;
                messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                if (data.message.includes('success')) {
                    this.reset();
                    setTimeout(() => {
                        messageContainer.innerHTML = '';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message-container').innerHTML = "<p class='error'>An error occurred. Please try again.</p>";
                button.disabled = false;
            });
        });
    </script>
</body>
</html>