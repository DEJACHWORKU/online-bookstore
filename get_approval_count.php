<?php
session_start();
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("get_approval_count - Connection failed: " . $conn->connect_error);
    echo json_encode(['count' => 0, 'error' => 'Database connection failed']);
    exit;
}

// Use the same filters as user/user approve.php
$search_department = isset($_GET['department']) ? $_GET['department'] : '';
$search_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

$sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
$params = [];
$types = '';

if ($search_department !== '') {
    $sql .= " AND department = ?";
    $params[] = $search_department;
    $types .= 's';
}

if ($search_academic_year !== '') {
    $sql .= " AND academic_year = ?";
    $params[] = $search_academic_year;
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$approval_count = $row['total'];

error_log("get_approval_count - Total displayed users: $approval_count, Filters: Dept=$search_department, Year=$search_academic_year");

$stmt->close();
$conn->close();

echo json_encode(['count' => $approval_count]);
?>