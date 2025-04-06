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
        
        <div class="profile-image-container">
            <div class="profile-image-placeholder">
                <i class="fas fa-user-circle"></i>
                <input type="file" id="profileImage" name="profileImage" accept="image/*" style="display: none;">
            </div>
            <label for="profileImage" class="upload-label">Upload Photo</label>
        </div>
        
        <form id="registrationForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                    <i class="fas fa-user input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="adminID">Personal ID</label>
                    <input type="text" id="adminID" name="adminID" required>
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
                <div class="form-group confirm-password">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="password-toggle" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn">Register</button>
            </div>
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
        
        // Profile image upload functionality
        document.querySelector('.profile-image-placeholder').addEventListener('click', function() {
            document.getElementById('profileImage').click();
        });

        document.getElementById('profileImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const placeholder = document.querySelector('.profile-image-placeholder');
                    placeholder.innerHTML = ''; // Clear the icon
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    placeholder.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            alert('Registration successful!');
        });
    </script>
</body>
</html>