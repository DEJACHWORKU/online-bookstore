<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['book_id']) || !isset($_POST['rating'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$book_id = (int)$_POST['book_id'];
$rating = (int)$_POST['rating'];
$user_id = (int)$_SESSION['user_id'];

if ($rating < 1 || $rating > 5) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid rating value']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if book exists
$check_book = $conn->prepare("SELECT id FROM books WHERE id = ?");
$check_book->bind_param("i", $book_id);
$check_book->execute();
$check_book->store_result();

if ($check_book->num_rows === 0) {
    $check_book->close();
    $conn->close();
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['success' => false, 'message' => 'Book not found']);
    exit;
}
$check_book->close();

// Check if user already rated this book
$check_rating = $conn->prepare("SELECT id FROM book_ratings WHERE book_id = ? AND user_id = ?");
$check_rating->bind_param("ii", $book_id, $user_id);
$check_rating->execute();
$check_rating->store_result();

if ($check_rating->num_rows > 0) {
    // Update existing rating
    $stmt = $conn->prepare("UPDATE book_ratings SET rating = ? WHERE book_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $rating, $book_id, $user_id);
} else {
    // Insert new rating
    $stmt = $conn->prepare("INSERT INTO book_ratings (book_id, user_id, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $book_id, $user_id, $rating);
}

$success = $stmt->execute();
$stmt->close();

if ($success) {
    // Get updated average and count
    $avg_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(rating) as rating_count FROM book_ratings WHERE book_id = ?");
    $avg_stmt->bind_param("i", $book_id);
    $avg_stmt->execute();
    $result = $avg_stmt->get_result();
    $data = $result->fetch_assoc();
    $avg_stmt->close();
    
    echo json_encode([
        'success' => true,
        'avg_rating' => (float)$data['avg_rating'],
        'rating_count' => (int)$data['rating_count']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save rating']);
}

$conn->close();
?>