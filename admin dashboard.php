<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_DB"; 

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    // Count comments
    $comment_count = 0;
    $result = $conn->query("SELECT COUNT(*) as count FROM comment"); 
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $comment_count = $row['count'];
    }

    // Initial count of users displayed on user approve.php (without filters for initial load)
    $approval_count = 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $approval_count = $row['total'];
    }
    error_log("Dashboard - Initial approval count (total users): $approval_count");

    $conn->close();
    ?>

    <div class="header">
        Welcome to Admin Dashboard - System Over Control Page
    </div>
    <div class="hamburger">☰</div>
    <div class="container">
        <div class="sidebar">
            <div class="menu">
                <a href="view-profile.php" class="menu-item active">Your Profile</a>
                <a href="user/admin register form.php" class="menu-item">Add Admin</a>
                <a href="user/librarian register form.php" class="menu-item">Add Librarian</a>
                <a href="user/user register form.php" class="menu-item">Add User</a>
                <a href="add book.php" class="menu-item">Add Book</a>
                <a href="user/manage admin.php" class="menu-item">Manage Admin</a>
                <a href="user/manage librarian.php" class="menu-item">Manage Librarian</a>
                <a href="user/manage user.php" class="menu-item">Manage User</a>
                <a href="manage book.php" class="menu-item">Manage Book</a>
                <a href="view-comment.php" class="menu-item">
                    View Comment
                    <span class="comment-count" data-count="<?php echo $comment_count; ?>" style="position: relative; display: inline-block; margin-left: 5px;">
                        <i class="fas fa-bell notification-icon"></i>
                        <?php if ($comment_count > 0): ?>
                            <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; font-weight: bold;">
                                <?php echo $comment_count; ?>
                            </span>
                        <?php endif; ?>
                    </span>
                </a>
                <a href="user/user approve.php" class="menu-item">
                    User Approval
                    <span class="approval-count" data-count="<?php echo $approval_count; ?>" style="position: relative; display: inline-block; margin-left: 5px;">
                        <i class="fas fa-bell notification-icon"></i>
                        <?php if ($approval_count > 0): ?>
                            <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; font-weight: bold;">
                                <?php echo $approval_count; ?>
                            </span>
                        <?php endif; ?>
                    </span>
                </a>
                <a href="index.php" class="menu-item logout">Logout</a>
            </div>
        </div>
        <div class="content-area">
            <iframe id="contentFrame" class="content-frame active" src="view-profile.php"></iframe>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item:not(.logout)');
            const logoutBtn = document.querySelector('.menu-item.logout');
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');
            const contentFrame = document.getElementById('contentFrame');
            const contentArea = document.querySelector('.content-area');

            function initSidebar() {
                sidebar.classList.remove('hidden'); 
                hamburger.style.display = 'none';   
                contentArea.classList.remove('full-width');
            }
            initSidebar();

            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.remove('hidden');
                contentArea.classList.remove('full-width');
                hamburger.style.display = 'none';
            });

            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    contentFrame.classList.remove('active');
                    setTimeout(() => {
                        contentFrame.src = this.getAttribute('href');
                        contentFrame.onload = () => {
                            contentFrame.classList.add('active');
                        };
                    }, 200);
                    sidebar.classList.add('hidden');
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                });
            });

            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });

            document.addEventListener('click', function(e) {
                if (!sidebar.classList.contains('hidden') && 
                    !sidebar.contains(e.target) && 
                    !hamburger.contains(e.target)) {
                    sidebar.classList.add('hidden');
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                }
            });

            window.addEventListener('resize', function() {
                if (sidebar.classList.contains('hidden')) {
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                } else {
                    contentArea.classList.remove('full-width');
                    hamburger.style.display = 'none';
                }
            });

            function updateCommentCount() {
                fetch('get_comment_count.php')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Comment count:', data.count);
                        const countElement = document.querySelector('.comment-count');
                        if (countElement) {
                            countElement.setAttribute('data-count', data.count);
                            const countBadge = countElement.querySelector('.notification-badge');
                            if (data.count > 0) {
                                if (!countBadge) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'notification-badge';
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
                    })
                    .catch(error => console.error('Error fetching comment count:', error));
            }

            function updateApprovalCount() {
                // Fetch with current filters if available
                const url = new URL('get_approval_count.php', window.location.origin);
                const params = new URLSearchParams(window.location.search);
                if (params.has('department')) url.searchParams.set('department', params.get('department'));
                if (params.has('academic_year')) url.searchParams.set('academic_year', params.get('academic_year'));

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Approval count from fetch:', data.count);
                        const countElement = document.querySelector('.approval-count');
                        if (countElement) {
                            countElement.setAttribute('data-count', data.count);
                            const countBadge = countElement.querySelector('.notification-badge');
                            if (data.count > 0) {
                                if (!countBadge) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'notification-badge';
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
                    })
                    .catch(error => console.error('Error fetching approval count:', error));
            }

            updateCommentCount();
            updateApprovalCount();
            setInterval(() => {
                updateCommentCount();
                updateApprovalCount();
            }, 30000); // Update every 30 seconds
        });
    </script>
</body>
</html>