<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['count' => 0]));
}

function getExpirationDate($startDate, $accessPermission) {
    if ($accessPermission === 'Approved') {
        return date('Y-m-d', strtotime("+30 days", strtotime($startDate)));
    }
    $parts = explode(' ', $accessPermission);
    $duration = (int)$parts[0];
    $unit = $parts[1];
    $interval = ($unit === 'Week') ? "weeks" : "months";
    return date('Y-m-d', strtotime("+$duration $interval", strtotime($startDate)));
}

$currentDate = date('Y-m-d');
$count = 0;

$stmt = $conn->prepare("SELECT date, access_permission FROM users");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $expirationDate = getExpirationDate($row['date'], $row['access_permission']);
    $remainingSeconds = strtotime($expirationDate) - strtotime($currentDate);
    $remainingDays = floor($remainingSeconds / (24 * 60 * 60));
    
    if ($remainingDays <= 30) {
        $count++;
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>