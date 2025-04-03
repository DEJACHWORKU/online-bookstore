<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT b.*, n.availability, n.created_at 
        FROM books b 
        INNER JOIN notifications n ON b.id = n.book_id 
        ORDER BY n.created_at DESC";
$result = $conn->query($sql);
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/notification.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header id="header">
        <h1><i class="fas fa-book-reader"></i> Book Notifications</h1>
        <nav class="nav-menu">
            <a href="user.php"><i class="fas fa-arrow-left"></i> Back to Bookstore</a>
            <button class="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </nav>
    </header>

    <main class="reading-optimized" id="book-list">
        <div class="book-grid">
            <?php if (empty($books)): ?>
                <div class="no-books">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Notifications Yet</h3>
                    <p>No books have been notified yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <?php if (!empty($book['cover'])): ?>
                            <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" class="book-cover">
                        <?php else: ?>
                            <div class="default-cover">
                                <i class="fas fa-book"></i>
                            </div>
                        <?php endif; ?>
                        <div class="book-details">
                            <h2>title: <?php echo htmlspecialchars($book['title']); ?></h2>
                            <div class="book-meta-grid">
                                <div class="meta-item">
                                    <span class="label">author:</span>
                                    <span><?php echo htmlspecialchars($book['author']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="label">department:</span>
                                    <span><?php echo htmlspecialchars($book['department']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="label">published:</span>
                                    <span><?php echo date('M d, Y', strtotime($book['date'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="label">notified:</span>
                                    <span><?php echo date('M d, Y - H:i', strtotime($book['created_at'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="label">availability:</span>
                                    <span class="availability-badge">
                                        <?php 
                                        $availability = str_replace(
                                            ['week', 'weeks', 'month'], 
                                            ['Week', 'Weeks', 'Month'], 
                                            $book['availability']
                                        );
                                        echo $availability;
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <p class="book-description"><span class="label">description:</span> <?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></p>
                            <div class="book-actions">
                                <?php if ($book['is_read']): ?>
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" target="_blank" class="read-btn" rel="noopener noreferrer">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if ($book['is_download']): ?>
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" download="<?php echo htmlspecialchars($book['title'] . '.pdf'); ?>" class="download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
    document.querySelector('.logout').addEventListener('click', function() {
        window.location.href = 'logout.php';
    });
    </script>
</body>
</html>