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
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_Db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle deletion
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $stmt = $conn->prepare("DELETE FROM comment WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    }

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
        <div class="header-container">
            <h2 class="heading">View All<span> Comments</span></h2>
        </div>
        <div class="comment-container">
            <?php if (empty($comments)): ?>
                <div class="no-comments">
                    <h3>No Comments Available</h3>
                    <p>No comments have been submitted yet.</p>
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
                            <p><strong>Message Subject:</strong> <?php echo htmlspecialchars($comment['subject']); ?></p>
                            <p class="message"><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($comment['message'])); ?></p>
                            <button class="delete-btn" onclick="approveDelete(<?php echo $comment['id']; ?>, this)">Approve</button>
                            <button class="undo-btn" onclick="undoDelete(<?php echo $comment['id']; ?>, this)" style="display: none;">Undo</button>
                        </div>
                        <div class="countdown-notification" id="countdown-<?php echo $comment['id']; ?>" style="display: none;">
                            Deleting in <span class="countdown-timer">5</span> seconds
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deletionTimers = new Map();

            // Theme initialization
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

            // Theme switcher toggle
            const settingsToggle = document.getElementById('settings-toggle');
            const themeOptions = document.getElementById('theme-options');
            if (settingsToggle && themeOptions) {
                settingsToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
                });
            }

            // Theme selection
            document.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const theme = this.getAttribute('data-theme');
                    document.body.className = theme;
                    localStorage.setItem('bookstoreTheme', theme);
                    themeOptions.style.display = 'none';
                });
            });

            // Close theme options when clicking outside
            document.addEventListener('click', function(e) {
                if (settingsToggle && themeOptions && !settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                    themeOptions.style.display = 'none';
                }
            });

            // Approve deletion
            window.approveDelete = function(commentId, button) {
                const card = document.querySelector(`.comment-card[data-id="${commentId}"]`);
                const countdownNotification = document.getElementById(`countdown-${commentId}`);
                const countdownElement = countdownNotification.querySelector('.countdown-timer');
                const undoButton = card.querySelector('.undo-btn');
                const deleteButton = button;

                // Disable approve button, show undo button and countdown
                deleteButton.style.display = 'none';
                undoButton.style.display = 'inline-block';
                card.classList.add('approved');
                countdownNotification.style.display = 'block';

                let seconds = 5;
                countdownElement.textContent = seconds;

                const countdown = setInterval(() => {
                    seconds--;
                    countdownElement.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        deletionTimers.delete(commentId);
                        // Remove card from DOM
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            // Check if there are any comments left
                            const remainingComments = document.querySelectorAll('.comment-card');
                            if (remainingComments.length === 0) {
                                const commentContainer = document.querySelector('.comment-container');
                                commentContainer.innerHTML = `
                                    <div class="no-comments">
                                        <h3>No Comments Available</h3>
                                        <p>No comments have been submitted yet.</p>
                                    </div>
                                `;
                            }
                            // Send updated comment count to parent
                            if (window.parent) {
                                window.parent.postMessage({
                                    type: 'updateCommentCount',
                                    count: remainingComments.length
                                }, '*');
                            }
                        }, 300);

                        // Delete from database
                        fetch(`view-comment.php?delete_id=${commentId}`, {
                            method: 'GET'
                        }).then(response => response.json())
                          .then(data => {
                              if (!data.success) {
                                  console.error('Deletion failed');
                              }
                          }).catch(error => {
                              console.error('Deletion error:', error);
                          });
                    }
                }, 1000);

                deletionTimers.set(commentId, countdown);
            };

            // Undo deletion
            window.undoDelete = function(commentId, button) {
                const card = document.querySelector(`.comment-card[data-id="${commentId}"]`);
                const countdownNotification = document.getElementById(`countdown-${commentId}`);
                const deleteButton = card.querySelector('.delete-btn');

                // Clear the countdown and reset UI
                const countdown = deletionTimers.get(commentId);
                if (countdown) {
                    clearInterval(countdown);
                    deletionTimers.delete(commentId);
                }

                card.classList.remove('approved');
                countdownNotification.style.display = 'none';
                button.style.display = 'none';
                deleteButton.style.display = 'inline-block';
            };
        });
    </script>
</body>
</html>