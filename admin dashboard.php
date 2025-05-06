<?php
session_start();

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "Admin User";
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : null;

$sql = "SELECT profile_image FROM Admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $profile_image = $admin['profile_image'];
    $_SESSION['profile_image'] = $profile_image;
}
$stmt->close();

$comment_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM comment");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $comment_count = $row['count'];
}

$approval_count = 0;
$currentDate = date('Y-m-d');
$sql = "SELECT date, access_permission FROM users";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $accessPermission = $row['access_permission'];
        $startDate = $row['date'];

        if ($accessPermission === 'Approved') {
            $expirationDate = date('Y-m-d', strtotime("+30 days", strtotime($startDate)));
        } else {
            $parts = explode(' ', $accessPermission);
            if (count($parts) === 2 && is_numeric($parts[0])) {
                $duration = (int)$parts[0];
                $unit = $parts[1];
                $interval = ($unit === 'Week') ? "weeks" : "months";
                $expirationDate = date('Y-m-d', strtotime("+$duration $interval", strtotime($startDate)));
            } else {
                continue;
            }
        }

        $remainingSeconds = strtotime($expirationDate) - strtotime($currentDate);
        $remainingDays = floor($remainingSeconds / (24 * 60 * 60));

        if ($remainingDays <= 30) {
            $approval_count++;
        }
    }
}
error_log("Dashboard - Approval count (30 days or less): $approval_count");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin dashboard.css">
    <link rel="stylesheet" href="css/themes.css">
</head>
<body class="theme-switcher">
    <div class="header">
        <div class="profile-container">
            <?php if ($profile_image && file_exists($_SERVER['DOCUMENT_ROOT'] . "/bookstore/book/Admin/" . $profile_image)): ?>
                <img src="/bookstore/book/Admin/<?php echo htmlspecialchars($profile_image); ?>" 
                     alt="Profile image of <?php echo htmlspecialchars($admin_name); ?>" 
                     class="profile-image">
            <?php else: ?>
                <div class="profile-image initials">
                    <?php 
                        $initials = '';
                        $names = explode(' ', $admin_name);
                        foreach ($names as $n) {
                            $initials .= strtoupper(substr($n, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                        echo htmlspecialchars($initials);
                    ?>
                </div>
            <?php endif; ?>
            <a href="admin profile.php" class="profile-button">View Details</a>
        </div>
        <span class="header-text">Welcome to Admin Dashboard - System Over Control Page</span>
    </div>
    <div class="hamburger" aria-label="Show menu">☰</div>
    <div class="container">
        <div class="sidebar active">
            <div class="menu">
                <a href="user/admin register form.php" class="menu-item">Add Admin</a>
                <a href="user/librarian register form.php" class="menu-item">Add Librarian</a>
                <a href="user/user register form.php" class="menu-item">Add User</a>
                <a href="dep't.php" class="menu-item">Add Department</a>
                <a href="add book.php" class="menu-item">Add Book</a>
                <a href="user/manage admin.php" class="menu-item">Manage Admin</a>
                <a href="user/manage librarian.php" class="menu-item">Manage Librarian</a>
                <a href="user/manage user.php" class="menu-item">Manage User</a>
                <a href="manage dep't.php" class="menu-item">Manage Department</a>
                <a href="manage book.php" class="menu-item">Manage Book</a>
                <a href="notify-user.php" class="menu-item">notify users</a>
                <a href="view-comment.php" class="menu-item">View Comment
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
                <a href="logout.php" class="menu-item logout">Logout</a>
            </div>
        </div>
        <div class="content-area">
            <iframe id="contentFrame" class="content-frame active" src="user/admin register form.php"></iframe>
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
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = 'theme-switcher ' + savedTheme;
            }
            sidebar.classList.add('active');
            hamburger.style.display = 'none';
            const firstMenuItem = menuItems[0];
            firstMenuItem.classList.add('active');
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
                hamburger.style.display = sidebar.classList.contains('active') ? 'none' : 'block';
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
                    sidebar.classList.remove('active');
                    hamburger.style.display = 'block';
                });
            });
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
            document.addEventListener('click', function(e) {
                if (sidebar.classList.contains('active') && 
                    !sidebar.contains(e.target) && 
                    !hamburger.contains(e.target)) {
                    sidebar.classList.remove('active');
                    hamburger.style.display = 'block';
                }
            });
            function updateCommentCount() {
                fetch('get_comment_count.php')
                    .then(response => response.json())
                    .then(data => {
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
                fetch('get_approval_count.php')
                    .then(response => response.json())
                    .then(data => {
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
            window.addEventListener('message', (event) => {
                if (event.data === 'updateCommentCount') {
                    updateCommentCount();
                }
            });
            updateCommentCount();
            updateApprovalCount();
            setInterval(() => {
                updateCommentCount();
                updateApprovalCount();
            }, 30000);
        });
    </script>
</body>
</html>