<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$result = $conn->query("SELECT COUNT(*) as count FROM comment");
$count = 0;
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>