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

$fullname = $department = $form_username = $academic_year = $remember_me = "";
$fullname_err = $department_err = $username_err = $academic_year_err = $remember_me_err = $password_err = $confirm_password_err = "";
$show_password_fields = false;
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['new_password'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $form_username = trim($_POST['username'] ?? '');
        $academic_year = trim($_POST['academic_year'] ?? '');
        $remember_me = trim($_POST['rememberMe'] ?? '');
        
        if (empty($fullname)) $fullname_err = "Full name is required";
        elseif (!preg_match('/^[a-zA-Z\s]+$/', $fullname)) $fullname_err = "Full name can only contain letters";

        if (empty($department)) $department_err = "Department is required";
        elseif (!preg_match('/^[a-zA-Z\s]+$/', $department)) $department_err = "Department can only contain letters";

        if (empty($form_username)) $username_err = "Username is required";

        if (empty($academic_year)) $academic_year_err = "Academic year is required";
        elseif (!preg_match('/^\d{4}$/', $academic_year)) $academic_year_err = "Academic year must be 4 digits";

        if (empty($remember_me)) $remember_me_err = "Security answer is required";

        if (empty($fullname_err) && empty($department_err) && empty($username_err) && 
            empty($academic_year_err) && empty($remember_me_err)) {
            
            $normalized_fullname = ucwords(strtolower($fullname));
            
            $stmt = $conn->prepare("SELECT * FROM users WHERE 
                full_name = ? AND 
                department = ? AND 
                username = ? AND 
                academic_year = ? AND 
                remember_me = ?");
            $stmt->bind_param("sssss", 
                $normalized_fullname, 
                $department, 
                $form_username, 
                $academic_year, 
                $remember_me
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $show_password_fields = true;
                $_SESSION['remember_me'] = $remember_me;
                $_SESSION['username'] = $form_username;
            } else {
                $stmt_fullname = $conn->prepare("SELECT full_name FROM users WHERE full_name = ?");
                $stmt_fullname->bind_param("s", $normalized_fullname);
                $stmt_fullname->execute();
                if ($stmt_fullname->get_result()->num_rows === 0) $fullname_err = "Full name does not match";
                $stmt_fullname->close();

                $stmt_department = $conn->prepare("SELECT department FROM users WHERE department = ?");
                $stmt_department->bind_param("s", $department);
                $stmt_department->execute();
                if ($stmt_department->get_result()->num_rows === 0) $department_err = "Department does not match";
                $stmt_department->close();

                $stmt_username = $conn->prepare("SELECT username FROM users WHERE username = ?");
                $stmt_username->bind_param("s", $form_username);
                $stmt_username->execute();
                if ($stmt_username->get_result()->num_rows === 0) $username_err = "Username does not match";
                $stmt_username->close();

                $stmt_academic_year = $conn->prepare("SELECT academic_year FROM users WHERE academic_year = ?");
                $stmt_academic_year->bind_param("s", $academic_year);
                $stmt_academic_year->execute();
                if ($stmt_academic_year->get_result()->num_rows === 0) $academic_year_err = "Academic year does not match";
                $stmt_academic_year->close();

                $stmt_remember_me = $conn->prepare("SELECT remember_me FROM users WHERE remember_me = ?");
                $stmt_remember_me->bind_param("s", $remember_me);
                $stmt_remember_me->execute();
                if ($stmt_remember_me->get_result()->num_rows === 0) $remember_me_err = "Security answer does not match";
                $stmt_remember_me->close();
            }
            $stmt->close();
        }
    } else {
        $form_username = trim($_POST['username'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        if (empty($new_password)) $password_err = "New password is required";
        elseif (strlen($new_password) < 6) $password_err = "Password must be at least 6 characters";

        if (empty($confirm_password)) $confirm_password_err = "Confirm password is required";
        elseif ($new_password !== $confirm_password) $confirm_password_err = "Passwords do not match";

        if (empty($password_err) && empty($confirm_password_err)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashed_password, $form_username);
            
            if ($stmt->execute() && $stmt->affected_rows === 1) {
                $success_message = "Password reset successfully!";
                session_unset();
                session_destroy();
                $show_password_fields = false;
            } else {
                $password_err = "Error updating password";
                $show_password_fields = true;
            }
            $stmt->close();
        } else {
            $show_password_fields = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
  <link rel="stylesheet" href="css/forgot user.css">
</head>
<body>
    <div class="container">
        <?php if ($success_message) { ?>
            <h2>Forgot Password</h2>
            <div class="success-message"><?php echo $success_message; ?></div>
            <a href="index.php" class="back-link">Go Back to Login</a>
        <?php } elseif (!$show_password_fields) { ?>
            <h2>Forgot Password</h2>
            <form method="POST" action="" onsubmit="return validateFirstForm()">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required placeholder="Enter full name">
                    <span class="error"><?php echo $fullname_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($department); ?>" required placeholder="Enter department">
                    <span class="error"><?php echo $department_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($form_username); ?>" required placeholder="Enter username">
                    <span class="error"><?php echo $username_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <input type="text" name="academic_year" id="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>" required placeholder="Enter academic year" maxlength="4">
                    <span class="error"><?php echo $academic_year_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="remember_me">Remember Me</label>
                    <input type="text" name="rememberMe" id="remember_me" value="<?php echo htmlspecialchars($remember_me); ?>" required placeholder="Enter security answer">
                    <span class="error"><?php echo $remember_me_err; ?></span>
                </div>
                
                <button type="submit" class="btn">Verify Details</button>
            </form>
        <?php } else { ?>
            <h2>Reset Password</h2>
            <form method="POST" action="" onsubmit="return validatePasswordForm()">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($form_username); ?>">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required placeholder="Enter new password">
                    <span class="toggle-password" onclick="togglePassword('new_password')">👁️</span>
                    <span class="error"><?php echo $password_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm new password">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                    <span class="error"><?php echo $confirm_password_err; ?></span>
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
                <a href="index.php" class="back-link">Back to Login</a>
            </form>
        <?php } ?>
    </div>

    <script>
        function validateFirstForm() {
            let inputs = document.querySelectorAll('input[type="text"]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.nextElementSibling.textContent = input.id === 'fullname' ? "Full name is required" : 
                                                          input.id === 'department' ? "Department is required" : 
                                                          input.id === 'username' ? "Username is required" : 
                                                          input.id === 'academic_year' ? "Academic year is required" : 
                                                          "Security answer is required";
                    fadeOutError(input.nextElementSibling);
                    valid = false;
                } else {
                    if (input.id === 'fullname' && !/^[a-zA-Z\s]+$/.test(input.value)) {
                        input.nextElementSibling.textContent = "Full name can only contain letters";
                        fadeOutError(input.nextElementSibling);
                        valid = false;
                    }
                    if (input.id === 'department' && !/^[a-zA-Z\s]+$/.test(input.value)) {
                        input.nextElementSibling.textContent = "Department can only contain letters";
                        fadeOutError(input.nextElementSibling);
                        valid = false;
                    }
                    if (input.id === 'academic_year' && !/^\d{4}$/.test(input.value)) {
                        input.nextElementSibling.textContent = "Academic year must be 4 digits";
                        fadeOutError(input.nextElementSibling);
                        valid = false;
                    }
                }
            });
            
            return valid;
        }

        function validatePasswordForm() {
            let newPass = document.getElementById('new_password').value;
            let confirmPass = document.getElementById('confirm_password').value;
            let errorSpanNew = document.getElementById('new_password').nextElementSibling.nextElementSibling;
            let errorSpanConfirm = document.getElementById('confirm_password').nextElementSibling.nextElementSibling;
            let valid = true;
            
            if (!newPass.trim()) {
                errorSpanNew.textContent = "New password is required";
                fadeOutError(errorSpanNew);
                valid = false;
            } else if (newPass.length < 6) {
                errorSpanNew.textContent = "Password must be at least 6 characters";
                fadeOutError(errorSpanNew);
                valid = false;
            } else {
                errorSpanNew.textContent = "";
            }
            
            if (!confirmPass.trim()) {
                errorSpanConfirm.textContent = "Confirm password is required";
                fadeOutError(errorSpanConfirm);
                valid = false;
            } else if (newPass !== confirmPass) {
                errorSpanConfirm.textContent = "Passwords do not match";
                fadeOutError(errorSpanConfirm);
                valid = false;
            } else {
                errorSpanConfirm.textContent = "";
            }
            
            return valid;
        }

        function togglePassword(id) {
            let input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
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
            document.querySelectorAll('.error').forEach(error => {
                if (error.textContent) {
                    fadeOutError(error);
                }
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>