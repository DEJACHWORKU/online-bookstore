<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root"; // TODO: Replace with a secure user
    $password = "";     // TODO: Replace with a secure password
    $dbname = "online_book_Db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $comment_count = 0;
    $result = $conn->query("SELECT COUNT(*) as count FROM comment");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $comment_count = $row['count'];
    }
    $conn->close();
    ?>
    
    <header id="header">
        <h1><i class="fas fa-book-reader"></i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="librarian.php">Go back</a>
            <a href="#" data-page="manage dep't.php">Manage department</a>
            <a href="#" data-page="user/manage user.php">Manage User</a>
            <a href="#" data-page="manage book.php" class="active">Manage Book</a>
            <a href="#" data-page="notify-user.php">Notify Users</a>
            <a href="#" data-page="view-comment.php">
                View Comment 
                <span class="comment-count" data-count="<?php echo $comment_count; ?>" style="position: relative; display: inline-block;">
                    <i class="fas fa-bell notification-icon"></i>
                    <?php if ($comment_count > 0): ?>
                        <span class="count-badge" style="position: absolute; top: -8px; right: -8px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; font-weight: bold; transition: transform 0.2s;">
                            <?php echo $comment_count; ?>
                        </span>
                    <?php endif; ?>
                </span>
            </a>
            <a href="index.php" class="logout">Logout</a>
        </nav>
    </header>
    
    <div class="content-container">
        <div class="iframe-container">
            <iframe id="contentFrame" src="manage book.php" frameborder="0" title="Content Frame"></iframe>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".nav-menu");
            const navLinks = document.querySelectorAll('.nav-menu a[data-page]');
            const contentFrame = document.getElementById('contentFrame');
            const body = document.body;

            contentFrame.addEventListener('load', () => {
                contentFrame.classList.add('loaded');
            });

            hamburger.addEventListener("click", function(e) {
                e.stopPropagation();
                navMenu.classList.toggle("active");
                body.classList.toggle("menu-open");
            });

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pageUrl = this.getAttribute('data-page');
                    
                    contentFrame.classList.remove('loaded');
                    setTimeout(() => {
                        contentFrame.src = pageUrl;
                    }, 300);

                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    if (navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                        body.classList.remove('menu-open');
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!navMenu.contains(e.target) && 
                    !hamburger.contains(e.target) && 
                    navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            });

            // Function to update comment count
            function updateCommentCount(count) {
                const countElement = document.querySelector('.comment-count');
                if (countElement) {
                    const parsedCount = parseInt(count, 10);
                    if (isNaN(parsedCount) || parsedCount < 0) {
                        console.error('Invalid comment count:', count);
                        return;
                    }

                    countElement.setAttribute('data-count', parsedCount);
                    let countBadge = countElement.querySelector('.count-badge');

                    if (parsedCount > 0) {
                        if (!countBadge) {
                            countBadge = document.createElement('span');
                            countBadge.className = 'count-badge';
                            countBadge.style.position = 'absolute';
                            countBadge.style.top = '-8px';
                            countBadge.style.right = '-8px';
                            countBadge.style.backgroundColor = 'red';
                            countBadge.style.color = 'white';
                            countBadge.style.borderRadius = '50%';
                            countBadge.style.padding = '2px 6px';
                            countBadge.style.fontSize = '12px';
                            countBadge.style.fontWeight = 'bold';
                            countBadge.style.transition = 'transform 0.2s';
                            countElement.appendChild(countBadge);
                        }
                        countBadge.textContent = parsedCount;
                        // Add a slight scale animation for visual feedback
                        countBadge.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            countBadge.style.transform = 'scale(1)';
                        }, 200);
                    } else if (countBadge) {
                        countBadge.style.transform = 'scale(0)';
                        setTimeout(() => {
                            countBadge.remove();
                        }, 200);
                    }
                }
            }

            // Listen for messages from the iframe
            window.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'updateCommentCount') {
                    updateCommentCount(event.data.count);
                }
            });

            // Handle SSE for real-time comment count updates
            const eventSource = new EventSource('comment_count_stream.php');
            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    updateCommentCount(data.count);
                } catch (error) {
                    console.error('Error parsing SSE data:', error);
                }
            };
            eventSource.onerror = function() {
                console.warn('SSE connection error. Retrying...');
                // Optionally, attempt to reconnect after a delay
                setTimeout(() => {
                    if (eventSource.readyState === EventSource.CLOSED) {
                        console.log('Reconnecting SSE...');
                        const newSource = new EventSource('comment_count_stream.php');
                        newSource.onmessage = eventSource.onmessage;
                        newSource.onerror = eventSource.onerror;
                        eventSource = newSource;
                    }
                }, 5000);
            };
        });
    </script>
</body>
</html>