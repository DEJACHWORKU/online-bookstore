<?php
ini_set('upload_max_filesize', '5M');
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
    $errors = [];
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
        $errors['fullName'] = "Full Name must contain only letters and cannot be empty.";
    }
    if (empty($personalID)) {
        $errors['personalID'] = "Personal ID is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email)) {
        $errors['email'] = "Valid email is required.";
    }
    if (!preg_match("/^[0-9]{10}$/", $phone) || empty($phone)) {
        $errors['phone'] = "Phone number must be exactly 10 digits.";
    }
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }
    if (strlen($password) < 6 || empty($password)) {
        $errors['password'] = "Password must be at least 6 characters long and is required.";
    }
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }

    if ($profileImage && $profileImage['error'] !== UPLOAD_ERR_OK && $profileImage['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors['profileImage'] = "Error uploading profile image.";
    } elseif ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
        $image_ext = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
        if (!in_array($image_ext, ['png', 'jpg', 'jpeg'])) {
            $errors['profileImage'] = "Profile image must be PNG, JPG, or JPEG.";
        }
        if ($profileImage['size'] > 5242880) {
            $errors['profileImage'] = "Profile image must be less than 5MB.";
        }
    }

    if (empty($errors)) {
        $check_fullname = $conn->prepare("SELECT id FROM Librarian WHERE full_name = ?");
        $check_fullname->bind_param("s", $fullName);
        $check_fullname->execute();
        $check_fullname->store_result();
        if ($check_fullname->num_rows > 0) {
            $errors['fullName'] = "Full Name already exists.";
        }
        $check_fullname->close();

        $check_personalid = $conn->prepare("SELECT id FROM Librarian WHERE personal_id = ?");
        $check_personalid->bind_param("s", $personalID);
        $check_personalid->execute();
        $check_personalid->store_result();
        if ($check_personalid->num_rows > 0) {
            $errors['personalID'] = "Personal ID already exists.";
        }
        $check_personalid->close();

        $check_phone = $conn->prepare("SELECT id FROM Librarian WHERE phone = ?");
        $check_phone->bind_param("s", $phone);
        $check_phone->execute();
        $check_phone->store_result();
        if ($check_phone->num_rows > 0) {
            $errors['phone'] = "Phone number already exists.";
        }
        $check_phone->close();

        $check_username = $conn->prepare("SELECT id FROM Librarian WHERE username = ?");
        $check_username->bind_param("s", $username);
        $check_username->execute();
        $check_username->store_result();
        if ($check_username->num_rows > 0) {
            $errors['username'] = "Username already exists.";
        }
        $check_username->close();

        $check_email = $conn->prepare("SELECT id FROM Librarian WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        if ($check_email->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
        $check_email->close();
    }

    if (empty($errors)) {
        $profileImagePath = "";
        if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
            $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/Librarian/';
            $image_dir = $base_dir . 'Uploads/profile_images/';
            $image_web_path = 'Uploads/profile_images/';
            
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true);
            }

            $image_filename = uniqid() . '.' . $image_ext;
            $image_path = $image_web_path . $image_filename;

            if (move_uploaded_file($profileImage['tmp_name'], $image_dir . $image_filename)) {
                $profileImagePath = $image_path;
            } else {
                $errors['profileImage'] = "Error uploading profile image! Please check your file permissions.";
            }
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Librarian (full_name, personal_id, email, phone, username, password, profile_image, remember_me) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullName, $personalID, $email, $phone, $username, $hashedPassword, $profileImagePath, $rememberMe);
            
            if ($stmt->execute()) {
                $message = "Librarian registration successful!";
            } else {
                $errors['general'] = "Error: " . $stmt->error;
                if ($profileImagePath && file_exists($image_dir . $image_filename)) {
                    unlink($image_dir . $image_filename);
                }
            }
            $stmt->close();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['message' => $message, 'errors' => $errors]);
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
    <link rel="stylesheet" href="../css/themes.css">
    <link rel="stylesheet" href="../css/admin register.css">
</head>
<body>
  
    <div class="container">
        <h1 class="title">Librarian Registration Form</h1>
        
        <form id="registrationForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profileImage">Profile Image (PNG/JPG, max 5MB, Optional):</label>
                <input type="file" id="profileImage" name="profileImage" accept="image/png, image/jpeg, image/jpg">
                <div class="error-message" id="profileImage-error"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                    <i class="fas fa-user input-icon"></i>
                    <div class="error-message" id="fullName-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="personalID">Personal ID</label>
                    <input type="text" id="personalID" name="personalID" placeholder="Enter your personal ID" required>
                    <i class="fas fa-id-card input-icon"></i>
                    <div class="error-message" id="personalID-error"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <i class="fas fa-envelope input-icon"></i>
                    <div class="error-message" id="email-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                    <i class="fas fa-phone input-icon"></i>
                    <div class="error-message" id="phone-error"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    <i class="fas fa-user-tag input-icon"></i>
                    <div class="error-message" id="username-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter at least 6 characters" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div class="error-message" id="password-error"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    <span class="password-toggle" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div class="error-message" id="confirmPassword-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="rememberMe">Remember Me</label>
                    <input type="text" id="rememberMe" name="rememberMe" placeholder="Enter Your security  Reference">
                    <i class="fas fa-check-circle input-icon"></i>
                    <div class="error-message"></div>
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

        document.addEventListener('DOMContentLoaded', function() {
            const settingsToggle = document.getElementById('settings-toggle');
            const themeOptions = document.getElementById('theme-options');

            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

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

            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;

                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                document.getElementById('message-container').textContent = '';

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        document.getElementById('message-container').innerHTML = 
                            `<p class="success">${data.message}</p>`;
                        if (data.message.includes('success')) {
                            this.reset();
                        }
                    }

                    if (data.errors) {
                        for (const [field, error] of Object.entries(data.errors)) {
                            const errorElement = document.getElementById(`${field}-error`);
                            if (errorElement) {
                                errorElement.textContent = error;
                                const inputField = document.getElementById(field);
                                if (inputField) {
                                    inputField.classList.add('error');
                                }
                            } else {
                                document.getElementById('message-container').innerHTML += 
                                    `<p class="error">${error}</p>`;
                            }
                        }
                    }

                    button.disabled = false;
                    document.getElementById('message-container').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest', 
                        inline: 'nearest' 
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message-container').innerHTML = 
                        "<p class='error'>An error occurred. Please try again.</p>";
                    button.disabled = false;
                });
            });
        });
    </script>
</body>
</html>