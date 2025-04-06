<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT b.* FROM books b LEFT JOIN notifications n ON b.id = n.book_id WHERE n.id IS NULL ORDER BY b.date DESC";
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
    <title>Notify Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/notifyuser.css">
</head>
<body>
    <section>
        <h1 class="heading">Notify Users About<span> Books</span></h1>
        <div id="notification-message"></div>
        <div class="book-container">
            <?php if (empty($books)): ?>
                <div class="no-books">
                    <h3>No Books Available</h3>
                    <p>Add some books to notify users!</p>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card" id="book-<?php echo $book['id']; ?>">
                        <div class="book-cover-container">
                            <?php if (!empty($book['cover'])): ?>
                                <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" class="book-cover">
                            <?php else: ?>
                                <div class="default-cover">
                                    <i class="fas fa-book"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-details">
                            <div class="book-title-header">
                                title: <?php echo htmlspecialchars($book['title']); ?>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">author:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($book['author']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">department:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($book['department']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">date added:</span>
                                <span class="detail-value"><?php echo date('M d, Y', strtotime($book['date'])); ?></span>
                            </div>
                            <div class="detail-row description-row">
                                <span class="detail-label">description:</span>
                                <span class="detail-value"><?php echo nl2br(htmlspecialchars($book['description'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">readable:</span>
                                <span class="detail-value"><?php echo $book['is_read'] ? 'Yes' : 'No'; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">downloadable:</span>
                                <span class="detail-value"><?php echo $book['is_download'] ? 'Yes' : 'No'; ?></span>
                            </div>
                            <div class="notification-card">
                                <form class="notify-form" onsubmit="sendNotification(event, <?php echo $book['id']; ?>)">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($book['title']); ?>">
                                    <div class="form-group">
                                        <label for="availability_<?php echo $book['id']; ?>" class="availability-label">Notification availability Time</label>
                                        <select name="availability" id="availability_<?php echo $book['id']; ?>" class="availability-select" required>
                                            <option value="" selected disabled>Select availability time</option>
                                            <option value="1min">1 Minute</option>
                                            <option value="1day">1 Day</option>
                                            <option value="1week">1 Week</option>
                                            <option value="2weeks">2 Weeks</option>
                                            <option value="3weeks">3 Weeks</option>
                                            <option value="1month">1 Month</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="notify-btn">
                                        <i class="fas fa-bell"></i> Notify Users
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
    function sendNotification(event, bookId) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const bookCard = document.getElementById('book-' + bookId);
        const notifyBtn = form.querySelector('.notify-btn');
        const bookTitle = form.querySelector('input[name="book_title"]').value;
        
        notifyBtn.disabled = true;
        notifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        fetch('notification.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const messageDiv = document.getElementById('notification-message');
            if (data.success) {
                const availabilityText = {
                    '1min': '1 Minute',
                    '1day': '1 Day',
                    '1week': '1 Week',
                    '2weeks': '2 Weeks', 
                    '3weeks': '3 Weeks',
                    '1month': '1 Month'
                }[data.availability] || data.availability;
                
                messageDiv.innerHTML = `
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <span>Successfully notified users about <strong>"${bookTitle}"</strong>! Available for ${availabilityText}.</span>
                    </div>
                `;
                
                bookCard.style.transition = 'all 0.4s ease';
                bookCard.style.opacity = '0';
                bookCard.style.height = '0';
                bookCard.style.margin = '0';
                bookCard.style.padding = '0';
                bookCard.style.overflow = 'hidden';
                
                setTimeout(() => {
                    bookCard.remove();
                    if (document.querySelectorAll('.book-card').length === 0) {
                        document.querySelector('.book-container').innerHTML = `
                            <div class="no-books">
                                <h3>All Books Notified</h3>
                                <p>No more books to notify users about.</p>
                            </div>
                        `;
                    }
                }, 400);
                
                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 6000);
            } else {
                messageDiv.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Failed to notify about <strong>"${bookTitle}"</strong>: ${data.message || 'Please try again later.'}</span>
                    </div>
                `;
                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 6000);
            }
        })
        .catch(error => {
            const messageDiv = document.getElementById('notification-message');
            messageDiv.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Error notifying about <strong>"${bookTitle}"</strong>: ${error.message || 'Network error occurred'}</span>
                </div>
            `;
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 6000);
        })
        .finally(() => {
            notifyBtn.disabled = false;
            notifyBtn.innerHTML = '<i class="fas fa-bell"></i> Notify Users';
        });
    }
    </script>
</body>
</html>