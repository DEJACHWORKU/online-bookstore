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
        $message = "<p style='color: red;'>Department already exists!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        if ($stmt->execute()) {
            $message = "<p style='color: green;'>New department added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e2f0d9);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px;
        }

        .container {
            text-align: center;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h1 {
            margin: 0 0 20px 0;
            color: #ff4757;
            font-size: 2.5rem;
            font-family: 'Arial', sans-serif;
            padding: 20px 0;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            border-bottom: 4px solid #4ecdc4;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #4ecdc4;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #ff6b6b;
            outline: none;
        }

        input[type="text"]::placeholder {
            color: #aaa;
        }

        button {
            background: #4ecdc4;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s, transform 0.3s;
            width: auto;
            margin-top: 10px;
        }

        button:hover {
            background: #ff6b6b;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }

            form {
                padding: 20px;
            }

            input[type="text"], button {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Department</h1>
        <form action="" method="post">
            <label for="dept_name">Department Name:</label>
            <input type="text" id="dept_name" name="dept_name" placeholder="Enter department name" required>
            <button type="submit">Add Department</button>
        </form>

        <?php if (isset($message)) echo $message; ?>
    </div>
</body>
</html>