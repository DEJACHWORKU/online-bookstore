<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Comments</title>
    <link rel="stylesheet" href="css/view-comment.css">
    <link rel="stylesheet" href="css/themes.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_Db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle delete request
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $stmt = $conn->prepare("DELETE FROM comment WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: view-comment.php"); // Refresh page after delete
        exit();
    }

    // Fetch comments
    $sql = "SELECT * FROM comment ORDER BY date DESC";
    $result = $conn->query($sql);

    $comments = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }

    $conn->close();
    ?>

    <section>
        <h2 class="heading">View All<span> Comments</span></h2>
        <div class="comment-container">
            <?php if (empty($comments)): ?>
                <div class="no-comments">
                    <h3>No Comments Yet</h3>
                    <p>Be the first to leave a comment!</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card" data-id="<?php echo $comment['id']; ?>">
                        <div class="comment-header">
                            <h3><?php echo htmlspecialchars($comment['full_name']); ?></h3>
                            <span class="date"><?php echo date('M d, Y - H:i', strtotime($comment['date'])); ?></span>
                        </div>
                        <div class="comment-details">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($comment['username']); ?></p>
                            <p><strong>Department:</strong> <?php echo htmlspecialchars($comment['department']); ?></p>
                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($comment['subject']); ?></p>
                            <p class="message"><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($comment['message'])); ?></p>
                            <button class="delete-btn" onclick="approveDelete(<?php echo $comment['id']; ?>)">Approve</button>
                        </div>
                        <div class="countdown-notification" id="countdown-<?php echo $comment['id']; ?>">
                            Deleting in <span class="countdown-timer">5</span> seconds
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function approveDelete(commentId) {
            if (confirm('Are you sure you want to approve and delete this comment? It will be removed in 5 seconds.')) {
                const card = document.querySelector(`.comment-card[data-id="${commentId}"]`);
                const countdownElement = document.getElementById(`countdown-${commentId}`).querySelector('.countdown-timer');
                card.classList.add('approved');

                let seconds = 5;
                countdownElement.textContent = seconds;

                const countdown = setInterval(() => {
                    seconds--;
                    countdownElement.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        window.location.href = `view-comment.php?delete_id=${commentId}`;
                    }
                }, 1000);
            }
        }

    
        window.addEventListener('load', () => {
            if (window.parent) {
                window.parent.postMessage('updateCommentCount', '*');
            }
        });
    </script>
</body>
</html>