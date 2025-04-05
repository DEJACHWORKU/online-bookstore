<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cleanupSql = "DELETE FROM notifications WHERE 
               (availability = '1min' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE)) OR
               (availability = '1day' AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)) OR
               (availability = '1week' AND created_at < DATE_SUB(NOW(), INTERVAL 1 WEEK)) OR
               (availability = '2weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 2 WEEK)) OR
               (availability = '3weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 3 WEEK)) OR
               (availability = '1month' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH))";
$conn->query($cleanupSql);

$sql = "SELECT b.*, n.availability, n.created_at as notification_date 
        FROM books b 
        JOIN notifications n ON b.id = n.book_id 
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
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="menu-bar">
                <a href="index.php" class="menu-btn home-btn">Go Home</a>
                <h1>Book <span>Notifications</span></h1>
                <a href="user.php" class="menu-btn store-btn">Go Bookstore</a>
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
                    try {
                        $now = new DateTime();
                        $createdAt = new DateTime($book['notification_date']);
                        $expiryDate = clone $createdAt;
                        
                        switch ($book['availability']) {
                            case '1min':
                                $expiryDate->add(new DateInterval('PT1M'));
                                break;
                            case '1day':
                                $expiryDate->add(new DateInterval('P1D'));
                                break;
                            case '1week':
                                $expiryDate->add(new DateInterval('P7D'));
                                break;
                            case '2weeks':
                                $expiryDate->add(new DateInterval('P14D'));
                                break;
                            case '3weeks':
                                $expiryDate->add(new DateInterval('P21D'));
                                break;
                            case '1month':
                                $expiryDate->add(new DateInterval('P1M'));
                                break;
                        }
                        
                        $remaining = $now->diff($expiryDate);
                        $remainingDays = $remaining->days;
                        $remainingHours = $remaining->h;
                        $remainingMinutes = $remaining->i;
                        
                        $availabilityText = str_replace(
                            ['min', 'day', 'week', 'month'], 
                            ['Minute', 'Day', 'Week', 'Month'], 
                            $book['availability']
                        );
                        
                        $remainingText = '';
                        if ($book['availability'] === '1min') {
                            $remainingText = $remainingMinutes > 0 ? "$remainingMinutes minutes left" : "Expiring soon";
                        } elseif ($book['availability'] === '1day') {
                            $remainingText = $remainingHours > 0 ? "$remainingHours hours left" : "Expiring soon";
                        } else {
                            $remainingText = $remainingDays > 0 ? "$remainingDays days left" : "Expiring soon";
                        }
                    } catch (Exception $e) {
                        $remainingDays = 0;
                        $remainingText = "Expiring soon";
                        $availabilityText = $book['availability'];
                        $expiryDate = new DateTime();
                    }
                ?>
                    <div class="notification-card" data-expiry="<?php echo $expiryDate->format('Y-m-d H:i:s'); ?>">
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
                                   Notification Available for <?php echo $availabilityText ?> (<?php echo $remainingText ?>)
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function checkExpiredNotifications() {
        const now = new Date();
        const notificationCards = document.querySelectorAll('.notification-card');
        
        notificationCards.forEach(card => {
            const expiryDateStr = card.getAttribute('data-expiry');
            
            try {
                const expiryDate = new Date(expiryDateStr);
                
                if (now > expiryDate) {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '0';
                    card.style.height = '0';
                    card.style.margin = '0';
                    card.style.padding = '0';
                    card.style.overflow = 'hidden';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        if (document.querySelectorAll('.notification-card').length === 0) {
                            const container = document.querySelector('.notification-container');
                            container.innerHTML = `
                                <div class="no-notifications">
                                    <i class="fas fa-bell-slash"></i>
                                    <h3>No Active Notifications</h3>
                                    <p>Check back later for new book announcements</p>
                                </div>
                            `;
                        }
                    }, 500);
                }
            } catch (e) {
                console.error('Error processing expiry date:', e);
            }
        });
    }
    
    setInterval(checkExpiredNotifications, 60000);
    document.addEventListener('DOMContentLoaded', checkExpiredNotifications);
    </script>
</body>
</html>