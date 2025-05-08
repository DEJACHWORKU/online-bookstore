<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "event: error\ndata: " . json_encode(['message' => 'Connection failed']) . "\n\n";
    ob_flush();
    flush();
    exit();
}

$last_count = -1;

while (true) {
    if ($conn->ping()) {
        $result = $conn->query("SELECT COUNT(*) as count FROM comment");
        $count = 0;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $count = $row['count'];
        }

        if ($count !== $last_count) {
            echo "data: " . json_encode(['count' => $count]) . "\n\n";
            ob_flush();
            flush();
            $last_count = $count;
        }
    } else {
        echo "event: error\ndata: " . json_encode(['message' => 'Database connection lost']) . "\n\n";
        ob_flush();
        flush();
        break;
    }

    // Sleep for 2 seconds to reduce server load
    sleep(2);
}

$conn->close();
?>