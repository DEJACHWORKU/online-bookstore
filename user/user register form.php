<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/user register.css">
</head>
<body>
    <div class="form-container">
        <h2>User Registration Form</h2>
        <form id="registrationForm" class="form-grid" onsubmit="return validateForm(event)" enctype="multipart/form-data">
            <!-- Image Upload Section -->
            <div class="image-upload-container">
                <div class="image-upload-circle" onclick="document.getElementById('profileImage').click()">
                    <i class="fas fa-user-circle upload-icon"></i>
                    <img id="imagePreview" src="#" alt="Profile Preview">
                    <input type="file" id="profileImage" name="profileImage" accept="image/*" onchange="previewImage(event)">
                </div>
                <span class="image-upload-text">Click to upload profile picture</span>
            </div>

            <!-- Form Fields -->
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" placeholder="Select date" required>
                <i class="fas fa-calendar-alt input-icon"></i>
            </div>
            <div class="form-group">
                <label for="academicYear">Academic Year</label>
                <input type="text" id="academicYear" name="academicYear" placeholder="YYYY-YYYY (e.g. 2023-2024)" required>
                <i class="fas fa-graduation-cap input-icon"></i>
            </div>
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                <i class="fas fa-user input-icon"></i>
            </div>
            <div class="form-group">
                <label for="idNumber">ID Number</label>
                <input type="text" id="idNumber" name="idNumber" placeholder="Enter your ID number" required>
                <i class="fas fa-id-card input-icon"></i>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" placeholder="Enter your department" required>
                <i class="fas fa-building input-icon"></i>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <select id="year" name="year" required>
                    <option value="">Select Year</option>
                    <option value="1">1<sup>st</sup></option>
                    <option value="2">2<sup>nd</sup></option>
                    <option value="3">3<sup>rd</sup></option>
                    <option value="4">4<sup>th</sup></option>
                    <option value="5">5<sup>th</sup></option>
                    <option value="6">6<sup>th</sup></option>
                    <option value="7">7<sup>th</sup></option>
                </select>
                <i class="fas fa-calendar input-icon"></i>
            </div>
            <div class="form-group">
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <option value="1">1<sup>st</sup></option>
                    <option value="2">2<sup>nd</sup></option>
                </select>
                <i class="fas fa-book input-icon"></i>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                <i class="fas fa-phone input-icon"></i>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
                <i class="fas fa-at input-icon"></i>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <i class="fas fa-lock input-icon"></i>
            </div>
            <div class="form-group full-width">
                <label for="accessPermission">User Access Permission (Months)</label>
                <select id="accessPermission" name="accessPermission" required>
                    <option value="">Select Duration</option>
                    <option value="1">1 Month</option>
                    <option value="2">2 Months</option>
                    <option value="3">3 Months</option>
                    <option value="4">4 Months</option>
                    <option value="5">5 Months</option>
                    <option value="6">6 Months</option>
                    <option value="7">7 Months</option>
                    <option value="8">8 Months</option>
                    <option value="9">9 Months</option>
                    <option value="10">10 Months</option>
                    <option value="11">11 Months</option>
                    <option value="12">12 Months</option>
                </select>
                <i class="fas fa-clock input-icon"></i>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>

    <script>
        function validateForm(event) {
            event.preventDefault();
            const form = document.getElementById('registrationForm');
            const date = form.date.value.trim();
            const academicYear = form.academicYear.value.trim();
            const fullName = form.fullName.value.trim();
            const idNumber = form.idNumber.value.trim();
            const phone = form.phone.value.trim();
            const username = form.username.value.trim();
            const password = form.password.value.trim();

            if (!date || !academicYear || !fullName || !idNumber || !phone || !username || !password) {
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Validate academic year format (YYYY-YYYY)
            if (!/^\d{4}-\d{4}$/.test(academicYear)) {
                alert('Academic Year must be in the format YYYY-YYYY (e.g. 2023-2024)');
                return false;
            }
            
            if (!/^\d+$/.test(phone)) {
                alert('Phone number must contain only digits.');
                return false;
            }
            if (password.length < 6) {
                alert('Password must be at least 6 characters long.');
                return false;
            }

            alert('Registration successful!');
            form.reset();
            // Also reset the image preview
            document.getElementById('imagePreview').style.display = 'none';
            document.querySelector('.upload-icon').style.display = 'block';
            return true;
        }

        function previewImage(event) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('imagePreview');
            const uploadIcon = document.querySelector('.upload-icon');
            
            reader.onload = function() {
                if (reader.readyState === 2) {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block';
                    uploadIcon.style.display = 'none';
                }
            }
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>