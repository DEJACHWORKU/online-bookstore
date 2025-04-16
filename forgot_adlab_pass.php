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

$role = $fullname = $id_field = $form_username = $remember_me = "";
$role_err = $fullname_err = $id_field_err = $username_err = $remember_me_err = "";
$password_err = $confirm_password_err = $new_remember_me_err = "";
$show_password_fields = false;
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['new_password'])) {
        $role = trim($_POST['role'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $id_field = trim($_POST['id_field'] ?? '');
        $form_username = trim($_POST['username'] ?? '');
        $remember_me = trim($_POST['rememberMe'] ?? '');
        
        if (empty($role) || !in_array($role, ['Admin', 'Librarian'])) {
            $role_err = "Please select a valid role";
        }
        if (empty($fullname)) {
            $fullname_err = "Full name is required";
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
            $fullname_err = "Full name can only contain letters";
        }
        if (empty($id_field)) {
            $id_field_err = $role === 'Admin' ? "Admin ID is required" : "Personal ID is required";
        }
        if (empty($form_username)) {
            $username_err = "Username is required";
        }
        if (empty($remember_me)) {
            $remember_me_err = "Security answer is required";
        }

        if (empty($role_err) && empty($fullname_err) && empty($id_field_err) && empty($username_err) && empty($remember_me_err)) {
            $normalized_fullname = ucwords(strtolower($fullname));
            $table = $role === 'Admin' ? 'Admin' : 'Librarian';
            $id_column = $role === 'Admin' ? 'admin_id' : 'personal_id';
            
            $stmt = $conn->prepare("SELECT * FROM $table WHERE full_name = ? AND $id_column = ? AND username = ? AND remember_me = ?");
            $stmt->bind_param("ssss", $normalized_fullname, $id_field, $form_username, $remember_me);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $show_password_fields = true;
                $_SESSION['role'] = $role;
                $_SESSION['username'] = $form_username;
            } else {
                $stmt_fullname = $conn->prepare("SELECT full_name FROM $table WHERE full_name = ?");
                $stmt_fullname->bind_param("s", $normalized_fullname);
                $stmt_fullname->execute();
                if ($stmt_fullname->get_result()->num_rows === 0) $fullname_err = "Full name is incorrect";
                $stmt_fullname->close();

                $stmt_id = $conn->prepare("SELECT $id_column FROM $table WHERE $id_column = ?");
                $stmt_id->bind_param("s", $id_field);
                $stmt_id->execute();
                if ($stmt_id->get_result()->num_rows === 0) $id_field_err = $role === 'Admin' ? "Admin ID is incorrect" : "Personal ID is incorrect";
                $stmt_id->close();

                $stmt_username = $conn->prepare("SELECT username FROM $table WHERE username = ?");
                $stmt_username->bind_param("s", $form_username);
                $stmt_username->execute();
                if ($stmt_username->get_result()->num_rows === 0) $username_err = "Username is incorrect";
                $stmt_username->close();

                $stmt_remember_me = $conn->prepare("SELECT remember_me FROM $table WHERE remember_me = ?");
                $stmt_remember_me->bind_param("s", $remember_me);
                $stmt_remember_me->execute();
                if ($stmt_remember_me->get_result()->num_rows === 0) $remember_me_err = "Security answer is incorrect";
                $stmt_remember_me->close();
            }
            $stmt->close();
        }
    } else {
        $role = $_SESSION['role'] ?? '';
        $form_username = trim($_POST['username'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $new_remember_me = trim($_POST['new_remember_me'] ?? '');
        
        if (empty($new_password)) $password_err = "New password is required";
        elseif (strlen($new_password) < 6) $password_err = "Password must be at least 6 characters";

        if (empty($confirm_password)) $confirm_password_err = "Confirm password is required";
        elseif ($new_password !== $confirm_password) $confirm_password_err = "Passwords do not match";

        if (!empty($new_remember_me) && strlen($new_remember_me) < 3) {
            $new_remember_me_err = "Security answer must be at least 3 characters";
        }

        if (empty($password_err) && empty($confirm_password_err) && empty($new_remember_me_err)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $table = $role === 'Admin' ? 'Admin' : 'Librarian';
            if (!empty($new_remember_me)) {
                $stmt = $conn->prepare("UPDATE $table SET password = ?, remember_me = ? WHERE username = ?");
                $stmt->bind_param("sss", $hashed_password, $new_remember_me, $form_username);
            } else {
                $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE username = ?");
                $stmt->bind_param("ss", $hashed_password, $form_username);
            }
            
            if ($stmt->execute() && $stmt->affected_rows === 1) {
                $success_message = "Password reset successfully!" . (!empty($new_remember_me) ? " Security question updated." : "");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/forgot_AL.css">
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
                    <label for="role">Select Your Role</label>
                    <select name="role" id="role" required onchange="toggleIdField()">
                        <option value="">-- Select Role --</option>
                        <option value="Admin" <?php echo $role === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="Librarian" <?php echo $role === 'Librarian' ? 'selected' : ''; ?>>Librarian</option>
                    </select>
                    <span class="error"><?php echo $role_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required placeholder="Enter full name">
                    <i class="fas fa-user input-icon"></i>
                    <span class="error"><?php echo $fullname_err; ?></span>
                </div>
                
                <div class="form-group" id="id-field-group">
                    <label for="id_field" id="id_field_label"><?php echo $role === 'Admin' ? 'Admin ID' : ($role === 'Librarian' ? 'Personal ID' : 'ID'); ?></label>
                    <input type="text" name="id_field" id="id_field" value="<?php echo htmlspecialchars($id_field); ?>" required placeholder="Enter <?php echo $role === 'Admin' ? 'Admin ID' : ($role === 'Librarian' ? 'Personal ID' : 'ID'); ?>">
                    <i class="fas fa-id-card input-icon"></i>
                    <span class="error"><?php echo $id_field_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($form_username); ?>" required placeholder="Enter username">
                    <i class="fas fa-user-tag input-icon"></i>
                    <span class="error"><?php echo $username_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="remember_me">Remeber Me</label>
                    <input type="text" name="rememberMe" id="remember_me" value="<?php echo htmlspecialchars($remember_me); ?>" required placeholder="Enter security answer">
                    <i class="fas fa-check-circle input-icon"></i>
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
                    <span class="toggle-password" onclick="togglePassword('new_password')"><i class="fas fa-eye"></i></span>
                    <span class="error"><?php echo $password_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm new password">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')"><i class="fas fa-eye"></i></span>
                    <span class="error"><?php echo $confirm_password_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="new_remember_me">New Remember Me Question Answer (Optional)</label>
                    <input type="text" name="new_remember_me" id="new_remember_me" placeholder="Enter new security answer">
                    <i class="fas fa-check-circle input-icon"></i>
                    <span class="error"><?php echo $new_remember_me_err; ?></span>
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
                <a href="index.php" class="back-link">Back to Login</a>
            </form>
        <?php } ?>
    </div>

    <script>
        function validateFirstForm() {
            let role = document.getElementById('role').value;
            let inputs = document.querySelectorAll('input[type="text"]');
            let valid = true;

            if (!role || (role !== 'Admin' && role !== 'Librarian')) {
                document.getElementById('role').nextElementSibling.textContent = "Please select a valid role";
                fadeOutError(document.getElementById('role').nextElementSibling);
                valid = false;
            }

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.nextElementSibling.nextElementSibling.textContent = input.id === 'fullname' ? "Full name is required" : 
                                                                             input.id === 'id_field' ? (role === 'Admin' ? "Admin ID is required" : "Personal ID is required") : 
                                                                             input.id === 'username' ? "Username is required" : 
                                                                             "Security answer is required";
                    fadeOutError(input.nextElementSibling.nextElementSibling);
                    valid = false;
                } else {
                    if (input.id === 'fullname' && !/^[a-zA-Z\s]+$/.test(input.value)) {
                        input.nextElementSibling.nextElementSibling.textContent = "Full name can only contain letters";
                        fadeOutError(input.nextElementSibling.nextElementSibling);
                        valid = false;
                    }
                }
            });
            
            return valid;
        }

        function validatePasswordForm() {
            let newPass = document.getElementById('new_password').value;
            let confirmPass = document.getElementById('confirm_password').value;
            let newRememberMe = document.getElementById('new_remember_me').value;
            let errorSpanNew = document.getElementById('new_password').nextElementSibling.nextElementSibling;
            let errorSpanConfirm = document.getElementById('confirm_password').nextElementSibling.nextElementSibling;
            let errorSpanRememberMe = document.getElementById('new_remember_me').nextElementSibling.nextElementSibling;
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
            
            if (newRememberMe.trim() && newRememberMe.length < 3) {
                errorSpanRememberMe.textContent = "Security answer must be at least 3 characters";
                fadeOutError(errorSpanRememberMe);
                valid = false;
            } else {
                errorSpanRememberMe.textContent = "";
            }
            
            return valid;
        }

        function togglePassword(id) {
            let input = document.getElementById(id);
            let icon = input.nextElementSibling.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
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

        function toggleIdField() {
            let role = document.getElementById('role').value;
            let label = document.getElementById('id_field_label');
            let input = document.getElementById('id_field');
            if (role === 'Admin') {
                label.textContent = 'Admin ID';
                input.placeholder = 'Enter Admin ID';
            } else if (role === 'Librarian') {
                label.textContent = 'Personal ID';
                input.placeholder = 'Enter Personal ID';
            } else {
                label.textContent = 'ID';
                input.placeholder = 'Enter ID';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleIdField();
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