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
    <link rel="stylesheet" href="css/themes.css">
    <link rel="stylesheet" href="css/add dep't.css">
   
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