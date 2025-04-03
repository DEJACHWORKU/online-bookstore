<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get POST data
    $book_id = $_POST['book_id'] ?? null;
    $book_title = $_POST['book_title'] ?? 'Unknown Book';
    $availability = $_POST['availability'] ?? '1week';

    // Validate input
    if (!$book_id) {
        throw new Exception("Book ID is required");
    }

    // Calculate expiry date based on availability
    $expiry_date = date('Y-m-d H:i:s', strtotime("+$availability"));

    // Insert notification
    $stmt = $conn->prepare("INSERT INTO notifications (book_id, expiry_date, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $book_id, $expiry_date);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Notification created successfully',
            'book_title' => $book_title,
            'availability' => $availability,
            'expiry_date' => $expiry_date
        ]);
    } else {
        throw new Exception("Failed to create notification: " . $conn->error);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'book_title' => $_POST['book_title'] ?? 'Unknown Book'
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>