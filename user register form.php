<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(120deg, #1e90ff, #ff00cc, #00ff99);
            background-size: 300% 300%;
            animation: gradientFlow 12s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 700px;
            position: relative;
            overflow: hidden;
            animation: zoomIn 0.7s ease-out;
        }
        .form-container::before {
            content: '';
            position: absolute;
            top: -60%;
            left: -60%;
            width: 220%;
            height: 220%;
            background: radial-gradient(circle, rgba(30, 144, 255, 0.25), transparent);
            transform: rotate(45deg);
            pointer-events: none;
            z-index: 0;
        }
        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        h2 {
            text-align: center;
            color: #1e90ff;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            position: relative;
            z-index: 1;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        label {
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        input, select {
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            background: #f5f8fa;
            box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            background: #fff;
            box-shadow: 0 0 12px rgba(30, 144, 255, 0.6);
            outline: none;
        }
        select {
            appearance: none;
            background: #f5f8fa url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="7" viewBox="0 0 14 7"><path d="M0 0h14L7 7z" fill="gray"/></svg>') no-repeat right 14px center;
            background-size: 14px;
        }
        button {
            grid-column: 1 / -1;
            padding: 14px 0;
            background: linear-gradient(45deg, #ff00cc, #1e90ff);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.4s ease;
            width: 180px; /* Increased size */
            display: block;
            margin: 15px auto 0;
            box-shadow: 0 6px 20px rgba(255, 0, 204, 0.5);
            position: relative;
            overflow: hidden;
        }
        button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        button:hover::after {
            width: 300px;
            height: 300px;
        }
        button:hover {
            background: linear-gradient(45deg, #1e90ff, #ff00cc);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 144, 255, 0.7);
        }
        button:active {
            transform: translateY(1px);
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.4);
        }
        sup {
            font-size: 11px;
            vertical-align: super;
        }
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            input, select {
                padding: 12px;
                font-size: 14px;
            }
            button {
                width: 150px;
                padding: 12px 0;
                font-size: 14px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h2>User Registration Form</h2>
        <form id="registrationForm" class="form-grid" onsubmit="return validateForm(event)">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="idNumber">ID Number</label>
                <input type="text" id="idNumber" name="idNumber" required>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" required>
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
            </div>
            <div class="form-group">
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <option value="1">1<sup>st</sup></option>
                    <option value="2">2<sup>nd</sup></option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
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
            </div>
            <button type="submit">Register</button>
        </form>
    </div>

    <script>
        function validateForm(event) {
            event.preventDefault();
            const form = document.getElementById('registrationForm');
            const fullName = form.fullName.value.trim();
            const idNumber = form.idNumber.value.trim();
            const phone = form.phone.value.trim();
            const username = form.username.value.trim();
            const password = form.password.value.trim();

            if (!fullName || !idNumber || !phone || !username || !password) {
                alert('Please fill in all required fields.');
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
            return true;
        }
    </script>
</body>
</html>