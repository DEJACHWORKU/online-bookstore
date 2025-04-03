<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['count' => $result['count']]);
} catch(PDOException $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
}
$conn = null;
?>