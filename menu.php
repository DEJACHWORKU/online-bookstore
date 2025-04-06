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
    // Database connection to get comment count
    $servername = "localhost";
    $username = "root";
    $password = "";
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
            <a href="#" data-page="">Manage User</a>
            <a href="#" data-page="manage book.php" class="active">Manage Book</a>
            <a href="#" data-page="notify-user.php">Notify Users</a>
            <a href="#" data-page="view-comment.php">
                View Comment 
                <span class="comment-count" data-count="<?php echo $comment_count; ?>" style="position: relative; display: inline-block;">
                    <i class="fas fa-bell notification-icon"></i>
                    <?php if ($comment_count > 0): ?>
                        <span style="position: absolute; top: -8px; right: -8px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; font-weight: bold;">
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
            function updateCommentCount() {
                fetch('get_comment_count.php')
                    .then(response => response.json())
                    .then(data => {
                        const countElement = document.querySelector('.comment-count');
                        if (countElement) {
                            countElement.setAttribute('data-count', data.count);
                            // Update the visible count
                            const countBadge = countElement.querySelector('span');
                            if (data.count > 0) {
                                if (!countBadge) {
                                    const newBadge = document.createElement('span');
                                    newBadge.style.position = 'absolute';
                                    newBadge.style.top = '-8px';
                                    newBadge.style.right = '-8px';
                                    newBadge.style.backgroundColor = 'red';
                                    newBadge.style.color = 'white';
                                    newBadge.style.borderRadius = '50%';
                                    newBadge.style.padding = '2px 6px';
                                    newBadge.style.fontSize = '12px';
                                    newBadge.style.fontWeight = 'bold';
                                    newBadge.textContent = data.count;
                                    countElement.appendChild(newBadge);
                                } else {
                                    countBadge.textContent = data.count;
                                }
                            } else if (countBadge) {
                                countBadge.remove();
                            }
                        }
                    });
            }
            
            // Update count every 30 seconds
            setInterval(updateCommentCount, 30000);
        });
    </script>
</body>
</html>