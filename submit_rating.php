<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

if ($book_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if the user already rated this book
$check_stmt = $conn->prepare("SELECT id FROM book_ratings WHERE book_id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $book_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already rated this book']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// Insert the rating
$stmt = $conn->prepare("INSERT INTO book_ratings (book_id, user_id, rating) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $book_id, $user_id, $rating);
$success = $stmt->execute();

// Calculate the new average rating
$avg_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM book_ratings WHERE book_id = ?");
$avg_stmt->bind_param("i", $book_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$avg_row = $avg_result->fetch_assoc();
$avg_rating = $avg_row['avg_rating'] ?: 0;

if ($success) {
    echo json_encode(['success' => true, 'avg_rating' => $avg_rating]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit rating']);
}

$stmt->close();
$avg_stmt->close();
$conn->close();
?>