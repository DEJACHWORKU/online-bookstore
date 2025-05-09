<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];
    
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id IN ($placeholders)");
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'deleted_count' => $stmt->affected_rows]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        $conn->close();
        exit();
    }
}

$cleanupSql = "DELETE FROM notifications WHERE 
               (availability = '1min' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE)) OR
               (availability = '1day' AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)) OR
               (availability = '1week' AND created_at < DATE_SUB(NOW(), INTERVAL 1 WEEK)) OR
               (availability = '2weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 2 WEEK)) OR
               (availability = '3weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 3 WEEK)) OR
               (availability = '1month' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH))";
$conn->query($cleanupSql);

$sql = "SELECT b.*, n.availability, n.created_at as notification_date, n.id as notification_id,
        TIMESTAMPDIFF(SECOND, NOW(), 
            CASE n.availability
                WHEN '1min' THEN DATE_ADD(n.created_at, INTERVAL 1 MINUTE)
                WHEN '1day' THEN DATE_ADD(n.created_at, INTERVAL 1 DAY)
                WHEN '1week' THEN DATE_ADD(n.created_at, INTERVAL 1 WEEK)
                WHEN '2weeks' THEN DATE_ADD(n.created_at, INTERVAL 2 WEEK)
                WHEN '3weeks' THEN DATE_ADD(n.created_at, INTERVAL 3 WEEK)
                WHEN '1month' THEN DATE_ADD(n.created_at, INTERVAL 1 MONTH)
            END) as remaining_seconds
        FROM books b 
        JOIN notifications n ON b.id = n.book_id 
        HAVING remaining_seconds > 0
        ORDER BY n.created_at DESC";
$result = $conn->query($sql);
$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="css/themes.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="menu-bar">
                <a href="index.php" class="menu-btn home-btn">Go Home</a>
                <h1>Book <span>Notifications</span></h1>
                <a href="user.php" class="menu-btn store-btn">Go To Bookstore</a>
            </div>
        </div>
        
        <div class="notification-container">
            <?php if (empty($notifications)): ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No Active Notifications</h3>
                    <p>Check back later for new book announcements</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $book): 
                    $availabilityText = str_replace(
                        ['min', 'day', 'week', 'month'], 
                        ['Minute', 'Day', 'Week', 'Month'], 
                        $book['availability']
                    );
                    $remainingSeconds = $book['remaining_seconds'];
                    if ($book['availability'] === '1min') {
                        $remainingText = $remainingSeconds > 0 ? "$remainingSeconds seconds left" : "Expiring soon";
                    } elseif ($book['availability'] === '1day') {
                        $remainingHours = floor($remainingSeconds / 3600);
                        $remainingText = $remainingHours > 0 ? "$remainingHours hours left" : "Expiring soon";
                    } else {
                        $remainingDays = floor($remainingSeconds / 86400);
                        $remainingText = $remainingDays > 0 ? "$remainingDays days left" : "Expiring soon";
                    }
                ?>
                    <div class="notification-card" data-expiry-seconds="<?php echo $remainingSeconds; ?>" data-notification-id="<?php echo $book['notification_id']; ?>">
                        <div class="book-cover-container">
                            <?php if (!empty($book['cover'])): ?>
                                <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" class="book-cover">
                            <?php else: ?>
                                <div class="default-cover">
                                    <i class="fas fa-book"></i>
                                </div>
                            <?php endif; ?>
                            <span class="notification-badge">New</span>
                        </div>
                        
                        <div class="book-details">
                            <span class="detail-label">Title:</span>
                            <span class="book-title"><?php echo htmlspecialchars($book['title']); ?></span>
                            
                            <div class="detail-row">
                                <span class="detail-label">Author:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($book['author']); ?></span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Department:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($book['department']); ?></span>
                            </div>
                            
                            <div class="detail-row description-row">
                                <span class="detail-label">Description:</span>
                                <span class="detail-value"><?php echo nl2br(htmlspecialchars($book['description'])); ?></span>
                            </div>
                            <div class="notification-footer">
                                <div class="posted-date">
                                    Posted: <?php echo date('M d, Y H:i', strtotime($book['notification_date'])); ?>
                                </div>
                                <div class="availability-info">
                                    Notification Available for <?php echo $availabilityText ?> (<span class="remaining-time"><?php echo $remainingText ?></span>)
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function updateRemainingTime() {
        const notificationCards = document.querySelectorAll('.notification-card');
        let expiredIds = [];
        
        notificationCards.forEach(card => {
            let remainingSeconds = parseInt(card.getAttribute('data-expiry-seconds'));
            const notificationId = card.getAttribute('data-notification-id');
            const remainingTimeSpan = card.querySelector('.remaining-time');
            
            if (remainingSeconds <= 0) {
                expiredIds.push(notificationId);
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '0';
                card.style.height = '0';
                card.style.margin = '0';
                card.style.padding = '0';
                card.style.overflow = 'hidden';
                setTimeout(() => {
                    card.remove();
                    if (document.querySelectorAll('.notification-card').length === 0) {
                        document.querySelector('.notification-container').innerHTML = `
                            <div class="no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <h3>No Active Notifications</h3>
                                <p>Check back later for new book announcements</p>
                            </div>
                        `;
                    }
                }, 500);
                return;
            }
            
            remainingSeconds--;
            card.setAttribute('data-expiry-seconds', remainingSeconds);
            
            const availability = card.querySelector('.availability-info').textContent.includes('Minute') ? '1min' : 'other';
            let remainingText = '';
            
            if (availability === '1min') {
                remainingText = remainingSeconds > 0 ? `${remainingSeconds} seconds left` : 'Expiring soon';
            } else {
                const remainingDays = Math.floor(remainingSeconds / 86400);
                const remainingHours = Math.floor(remainingSeconds / 3600);
                remainingText = remainingDays > 0 ? `${remainingDays} days left` : remainingHours > 0 ? `${remainingHours} hours left` : 'Expiring soon';
            }
            
            remainingTimeSpan.textContent = remainingText;
        });
        
        if (expiredIds.length > 0) {
            fetch(location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: expiredIds }),
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error deleting expired notifications:', data.error);
                }
            })
            .catch(error => {
                console.error('Error deleting expired notifications:', error);
            });
        }
    }
    
    setInterval(updateRemainingTime, 1000);
    document.addEventListener('DOMContentLoaded', updateRemainingTime);
    </script>
</body>
</html>