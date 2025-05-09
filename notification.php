<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
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
    $notification_id = $stmt->insert_id;
    
    $remaining_seconds = 0;
    switch ($availability) {
        case '1min':
            $remaining_seconds = 60;
            break;
        case '1day':
            $remaining_seconds = 86400;
            break;
        case '1week':
            $remaining_seconds = 604800;
            break;
        case '2weeks':
            $remaining_seconds = 1209600;
            break;
        case '3weeks':
            $remaining_seconds = 1814400;
            break;
        case '1month':
            $remaining_seconds = 2592000; // Approximate 30 days
            break;
    }

    $stmt = $conn->prepare("SELECT created_at FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $created_at = $result->fetch_assoc()['created_at'] ?? 'NOW()';

    echo json_encode([
        'success' => true,
        'notification_id' => $notification_id,
        'availability' => $availability,
        'remaining_seconds' => $remaining_seconds,
        'created_at' => $created_at
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create notification']);
}

$stmt->close();
$conn->close();
?>