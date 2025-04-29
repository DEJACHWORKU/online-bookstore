<?php
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '6M');
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
    $input = [];

    $input['date'] = trim($_POST['date'] ?? '');
    $input['academicYear'] = trim($_POST['academicYear'] ?? '');
    $input['fullName'] = trim($_POST['fullName'] ?? '');
    $input['idNumber'] = trim($_POST['idNumber'] ?? '');
    $input['department'] = trim($_POST['department'] ?? '');
    $input['year'] = trim($_POST['year'] ?? '');
    $input['semester'] = trim($_POST['semester'] ?? '');
    $input['phone'] = trim($_POST['phone'] ?? '');
    $input['username'] = trim($_POST['username'] ?? '');
    $input['password'] = $_POST['password'] ?? '';
    $input['rememberMe'] = trim($_POST['rememberMe'] ?? '');
    $input['accessPermission'] = trim($_POST['accessPermission'] ?? '');

    $profileImage = $_FILES['profileImage'] ?? null;

    if (empty($input['date'])) {
        $errors['date'] = "Date is required";
    }

    if (empty($input['academicYear'])) {
        $errors['academicYear'] = "Academic year is required";
    } elseif (!preg_match('/^\d{4}$/', $input['academicYear'])) {
        $errors['academicYear'] = "Academic year must be 4 digits";
    }

    if (empty($input['fullName'])) {
        $errors['fullName'] = "Full name is required";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $input['fullName'])) {
        $errors['fullName'] = "Full name can only contain letters";
    } else {
        $input['fullName'] = ucwords(strtolower($input['fullName']));
    }

    if (empty($input['idNumber'])) {
        $errors['idNumber'] = "ID number is required";
    }

    if (empty($input['department'])) {
        $errors['department'] = "Department is required";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $input['department'])) {
        $errors['department'] = "Department can only contain letters";
    }

    $validYears = ['1', '2', '3', '4', '5', '6', '7'];
    if (empty($input['year'])) {
        $errors['year'] = "Year is required";
    } elseif (!in_array($input['year'], $validYears)) {
        $errors['year'] = "Invalid year selected";
    }

    $validSemesters = ['1', '2'];
    if (empty($input['semester'])) {
        $errors['semester'] = "Semester is required";
    } elseif (!in_array($input['semester'], $validSemesters)) {
        $errors['semester'] = "Invalid semester selected";
    }

    if (empty($input['phone'])) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^\d{10}$/', $input['phone'])) {
        $errors['phone'] = "Phone must be 10 digits";
    }

    if (empty($input['username'])) {
        $errors['username'] = "Username is required";
    }

    if (empty($input['password'])) {
        $errors['password'] = "Password is required";
    } elseif (strlen($input['password']) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if (empty($input['rememberMe'])) {
        $errors['rememberMe'] = "Remember Me is required";
    }

    $validPermissions = [
        '1 Week', '1 Month', '2 Months', '3 Months', '4 Months', '5 Months',
        '6 Months', '7 Months', '8 Months', '9 Months', '10 Months', '11 Months', '12 Months'
    ];
    if (empty($input['accessPermission'])) {
        $errors['accessPermission'] = "Access permission is required";
    } elseif (!in_array($input['accessPermission'], $validPermissions)) {
        $errors['accessPermission'] = "Invalid access permission selected";
    }

    if ($profileImage && $profileImage['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($profileImage['error'] !== UPLOAD_ERR_OK) {
            $errors['profileImage'] = "Error uploading file";
        } else {
            $image_ext = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
            if (!in_array($image_ext, ['png', 'jpg', 'jpeg', 'gif'])) {
                $errors['profileImage'] = "Only PNG, JPG, JPEG, and GIF allowed";
            }
            if ($profileImage['size'] > 5242880) {
                $errors['profileImage'] = "Image must be less than 5MB";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR phone = ? OR id_number = ? OR LOWER(full_name) = ?");
        $lowerFullName = strtolower($input['fullName']);
        $stmt->bind_param("ssss", $input['username'], $input['phone'], $input['idNumber'], $lowerFullName);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $checkStmt = $conn->prepare("SELECT username, phone, id_number, full_name FROM users WHERE username = ? OR phone = ? OR id_number = ? OR LOWER(full_name) = ? LIMIT 1");
            $checkStmt->bind_param("ssss", $input['username'], $input['phone'], $input['idNumber'], $lowerFullName);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $existingData = $result->fetch_assoc();
            $checkStmt->close();
            
            if (strtolower($existingData['full_name']) === $lowerFullName) {
                $errors['fullName'] = "This name is already registered";
            }
            if ($existingData['username'] === $input['username']) {
                $errors['username'] = "Username already exists";
            }
            if ($existingData['phone'] === $input['phone']) {
                $errors['phone'] = "Phone number already exists";
            }
            if ($existingData['id_number'] === $input['idNumber']) {
                $errors['idNumber'] = "ID number already exists";
            }
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $profileImagePath = "";
        if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
            $image_dir = '../book/users/';
            $image_web_path = 'users/';
            
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true);
            }

            $image_ext = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
            $image_filename = uniqid('user_') . '.' . $image_ext;
            $image_path = $image_web_path . $image_filename;

            if (move_uploaded_file($profileImage['tmp_name'], $image_dir . $image_filename)) {
                $profileImagePath = $image_path;
            } else {
                $errors['profileImage'] = "Failed to upload profile image! Please check file permissions.";
                error_log("Upload failed. Directory: " . $image_dir . " Permissions: " . substr(sprintf('%o', fileperms($image_dir)), -4));
            }
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (
                date, academic_year, full_name, id_number, department, 
                year, semester, phone, username, password, 
                remember_me, access_permission, profile_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param(
                "sssssssssssss",
                $input['date'],
                $input['academicYear'],
                $input['fullName'],
                $input['idNumber'],
                $input['department'],
                $input['year'],
                $input['semester'],
                $input['phone'],
                $input['username'],
                $hashedPassword,
                $input['rememberMe'],
                $input['accessPermission'],
                $profileImagePath
            );
            
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors['database'] = "Database error: " . $stmt->error;
                if ($profileImagePath && file_exists($image_dir . $image_filename)) {
                    unlink($image_dir . $image_filename);
                }
            }
            $stmt->close();
        }
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    }
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link rel="stylesheet" href="../css/user register.css">
    <link rel="stylesheet" href="../css/themes.css">

    <script src="../js/theme_switcher.js"></script>
</head>
<body>
    <div class="form-container">
        <h2>User Registration Form</h2>
        <form id="registrationForm" class="form-grid" method="post" enctype="multipart/form-data">
            <div style="grid-column: span 2;">
                <div class="image-upload-circle" onclick="document.getElementById('profileImage').click()">
                    <span id="uploadText">Click to upload photo</span>
                    <img id="imagePreview" src="#" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                    <input type="file" id="profileImage" name="profileImage" accept="image/*" style="display:none;" onchange="previewImage(event)">
                </div>
                <div id="profileImage-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required placeholder="Select date">
                <div id="date-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="academicYear">Academic Year</label>
                <input type="text" id="academicYear" name="academicYear" placeholder="e.g. 2023" required maxlength="4" pattern="\d{4}" title="Enter 4 digit year">
                <div id="academicYear-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="e.g. John Doe" required pattern="[a-zA-Z\s]+" title="Letters only">
                <div id="fullName-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="idNumber">ID Number</label>
                <input type="text" id="idNumber" name="idNumber" placeholder="Enter your ID number" required>
                <div id="idNumber-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="department">Department</label>
                <input type="text" id="department" name="department" placeholder="e.g. Computer Science" required pattern="[a-zA-Z\s]+" title="Letters only">
                <div id="department-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="year">Year</label>
                <select id="year" name="year" required>
                    <option value="">Select Year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                    <option value="5">5th Year</option>
                    <option value="6">6th Year</option>
                    <option value="7">7th Year</option>
                </select>
                <div id="year-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <option value="1">1st Semester</option>
                    <option value="2">2nd Semester</option>
                </select>
                <div id="semester-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="e.g. 0912345678" required pattern="\d{10}" title="10 digits only" maxlength="10">
                <div id="phone-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                <div id="username-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="password">Password (min 6 chars)</label>
                <input type="password" id="password" name="password" placeholder="Enter at least 6 characters" required minlength="6">
                <div id="password-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="rememberMe">Remember Me</label>
                <input type="text" id="rememberMe" name="rememberMe" placeholder="Enter remember me value" required>
                <div id="rememberMe-error" class="error-message"></div>
            </div>
            
            <div>
                <label for="accessPermission">Access Permission</label>
                <select id="accessPermission" name="accessPermission" required>
                    <option value="">Select Duration</option>
                    <option value="1 Week">1 Week</option>
                    <option value="1 Month">1 Month</option>
                    <option value="2 Months">2 Months</option>
                    <option value="3 Months">3 Months</option>
                    <option value="4 Months">4 Months</option>
                    <option value="5 Months">5 Months</option>
                    <option value="6 Months">6 Months</option>
                    <option value="7 Months">7 Months</option>
                    <option value="8 Months">8 Months</option>
                    <option value="9 Months">9 Months</option>
                    <option value="10 Months">10 Months</option>
                    <option value="11 Months">11 Months</option>
                    <option value="12 Months">12 Months</option>
                </select>
                <div id="accessPermission-error" class="error-message"></div>
            </div>
            
            <button type="submit" id="submitBtn" style="grid-column: span 2;">Register</button>
            <div id="form-message" style="grid-column: span 2;"></div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            const imagePreview = document.getElementById('imagePreview');
            const uploadText = document.getElementById('uploadText');
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                uploadText.style.display = 'none';
            }
            reader.readAsDataURL(file);
            
            const errorElement = document.getElementById('profileImage-error');
            if (file.size > 5 * 1024 * 1024) {
                errorElement.textContent = "Image must be less than 5MB";
                event.target.value = '';
                return;
            }
            
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                errorElement.textContent = "Only JPG, PNG, and GIF allowed";
                event.target.value = '';
                return;
            }
            
            errorElement.textContent = '';
        }

        function hideSuccessMessage() {
            setTimeout(() => {
                document.getElementById('form-message').textContent = '';
            }, 3000);
        }

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitBtn');
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('input, select').forEach(el => el.classList.remove('is-invalid'));
            
            submitBtn.disabled = true;
            submitBtn.textContent = "Processing...";
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('form-message').style.color = 'green';
                    document.getElementById('form-message').textContent = data.message;
                    form.reset();
                    document.getElementById('imagePreview').style.display = 'none';
                    document.getElementById('uploadText').style.display = 'block';
                    hideSuccessMessage();
                } else {
                    // Display errors for each field
                    if (data.errors) {
                        for (const [field, error] of Object.entries(data.errors)) {
                            const errorElement = document.getElementById(`${field}-error`);
                            const inputElement = document.getElementById(field);
                            if (errorElement && inputElement) {
                                errorElement.textContent = error;
                                inputElement.classList.add('is-invalid');
                            } else if (field === 'database') {
                                // Show database errors in the form message area
                                document.getElementById('form-message').style.color = 'red';
                                document.getElementById('form-message').textContent = error;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById('form-message').style.color = 'red';
                document.getElementById('form-message').textContent = "An error occurred. Please try again.";
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = "Register";
            });
        });

        // Input validation clearing as user types
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorElement = document.getElementById(`${this.id}-error`);
                if (errorElement) errorElement.textContent = '';
            });
        });
    </script>
</body>
</html>