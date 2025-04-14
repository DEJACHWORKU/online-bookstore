<?php
session_start();

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Librarian') {
    header("Location: login0.php");
    exit();
}

$librarian_name = isset($_SESSION['full_name']) && !empty($_SESSION['full_name']) ? $_SESSION['full_name'] : "Librarian";
$profile_image = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : null;
$base_path = '/bookstore/book/Librarian/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/librarian.css">
</head>
<body>
    <header id="header">
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <div class="profile-container">
            <?php if ($profile_image && file_exists($_SERVER['DOCUMENT_ROOT'] . $base_path . $profile_image)): ?>
                <img src="<?php echo htmlspecialchars($base_path . $profile_image); ?>" 
                     alt="Profile image of <?php echo htmlspecialchars($librarian_name); ?>" 
                     class="profile-image">
            <?php else: ?>
                <div class="profile-image initials">
                    <?php 
                        $initials = '';
                        $names = explode(' ', trim($librarian_name));
                        foreach ($names as $n) {
                            $initials .= strtoupper(substr($n, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                        echo htmlspecialchars($initials);
                    ?>
                </div>
            <?php endif; ?>
            <a href="librarian profile.php" class="view-details">View Details</a>
        </div>
        <nav class="nav-menu">
            <a href="login1.php" class=""><i class="fas fa-store"></i> Go to Store</a>
            <a href="#" data-page="dep't.php"><i class="fas fa-building"></i> Add Department</a>
            <a href="#" data-page="add book.php"><i class="fas fa-book-medical"></i> Add Book</a>
            <a href="#" data-page="user/user register form.php"><i class="fas fa-user-plus"></i> Add User</a>
            <a href="menu.php"><i class="fas fa-cog"></i> Go to Manage</a>
            <button class="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </nav>
    </header>
    
    <div class="content-container">
        <div class="iframe-container">
            <iframe id="contentFrame" src="dep't.php" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentFrame = document.getElementById('contentFrame');
            const navLinks = document.querySelectorAll('.nav-menu a[data-page]');
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            let currentPage = "dep't.php";

            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === currentPage) {
                    link.classList.add('active');
                }
            });

            document.querySelector('.logout').addEventListener('click', function() {
                fetch('logout.php', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'index.php';
                    } else {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    window.location.href = 'index.php';
                });
            });

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newPage = this.getAttribute('data-page');
                    
                    if (newPage !== currentPage) {
                        contentFrame.src = newPage;
                        navLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                        currentPage = newPage;
                    }
                    
                    if (window.innerWidth <= 768) {
                        navMenu.classList.remove('active');
                    }
                });
            });

            hamburger.addEventListener('click', (e) => {
                e.stopPropagation();
                navMenu.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#header') && window.innerWidth <= 768) {
                    navMenu.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>