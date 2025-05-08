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
                            <h3><strong>Full Name:</strong> <?php echo htmlspecialchars($comment['full_name']); ?></h3>
                            <span class="date"><?php echo date('M d, Y - H:i', strtotime($comment['date'])); ?></span>
                        </div>
                        <div class="comment-details">
                            <p><strong>ID Nmber:</strong> <?php echo htmlspecialchars($comment['username']); ?></p>
                            <p><strong>Department:</strong> <?php echo htmlspecialchars($comment['department']); ?></p>
                            <p><strong>Message Subject:</strong> <?php echo htmlspecialchars($comment['subject']); ?></p>
                            <p class="message"><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($comment['message'])); ?></p>
                            <button class="delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>, this)">Approve</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

            const settingsToggle = document.getElementById('settings-toggle');
            const themeOptions = document.getElementById('theme-options');
            if (settingsToggle && themeOptions) {
                settingsToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
                });
            }

            document.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    const theme = this.getAttribute('data-theme');
                    document.body.className = theme;
                    localStorage.setItem('bookstoreTheme', theme);
                    themeOptions.style.display = 'none';
                });
            });

            document.addEventListener('click', function(e) {
                if (settingsToggle && themeOptions && !settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                    themeOptions.style.display = 'none';
                }
            });

            window.deleteComment = function(commentId, button) {
                const card = button.closest('.comment-card');
                let countdown = 5;

                button.disabled = true;
                button.textContent = `Approving (${countdown}s)`;

                const countdownInterval = setInterval(() => {
                    countdown--;
                    button.textContent = `Approving (${countdown}s)`;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                    }
                }, 1000);

                setTimeout(() => {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        const remainingComments = document.querySelectorAll('.comment-card').length;
                        if (remainingComments === 0) {
                            const commentContainer = document.querySelector('.comment-container');
                            commentContainer.innerHTML = `
                                <div class="no-comments">
                                    <h3>No Comments Available</h3>
                                    <p>No comments have been submitted yet.</p>
                                </div>
                            `;
                        }
                        if (window.parent) {
                            window.parent.postMessage({
                                type: 'updateCommentCount',
                                count: remainingComments
                            }, '*');
                        }
                    }, 300);

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
                }, 5000);
            };
        });
    </script>
</body>
</html>