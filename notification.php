<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$book_id = $_POST['book_id'] ?? null;
$availability = $_POST['availability'] ?? null;

if (!$book_id || !$availability) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO notifications (book_id, availability, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $book_id, $availability);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'availability' => $availability]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create notification']);
}

$stmt->close();
$conn->close();
?>