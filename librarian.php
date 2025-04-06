<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/librarian.css">
  >
</head>
<body>
    <header id="header">
        <h1><i class="fas fa-book"></i> Librarian Dashboard</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="user.php">Go to Store</a>
            <a href="#" data-page="dep't.php">Add Department</a>
            <a href="#" data-page="manage dep't.php">Manage Dep't</a>
            <a href="#" data-page="add book.php">Add Book</a>
            <a href="#" data-page="user register form.php">Add User</a>
            <a href="menu.php">Go to Manage</a>
            <button class="logout">Logout</button>
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
            const header = document.getElementById('header');
            let currentPage = "dep't.php";
            let isAnimating = false;

            // Set initial active link
            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === currentPage) {
                    link.classList.add('active');
                }
            });

            // Logout button
            document.querySelector('.logout').addEventListener('click', function() {
                window.location.href = 'index.php';
            });

            // Navigation links
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (isAnimating) return;

                    const newPage = this.getAttribute('data-page');
                    
                    if (newPage !== currentPage) {
                        isAnimating = true;
                        
                        // Slide out current content to left
                        contentFrame.classList.add('slide-out-left');
                        
                        setTimeout(() => {
                            // Update content
                            contentFrame.src = newPage;
                            contentFrame.classList.remove('slide-out-left');
                            
                            // Slide in new content from right
                            contentFrame.classList.add('slide-in-right');
                            
                            // Update active link
                            navLinks.forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                            currentPage = newPage;
                            
                            // Clean up animation
                            contentFrame.addEventListener('animationend', function handler() {
                                contentFrame.classList.remove('slide-in-right');
                                isAnimating = false;
                                contentFrame.removeEventListener('animationend', handler);
                            });
                        }, 500); // Match the animation duration
                    }
                    
                    if (window.innerWidth <= 768) {
                        navMenu.classList.remove('active');
                    }
                });
            });

            // Hamburger menu toggle
            hamburger.addEventListener('click', (e) => {
                e.stopPropagation();
                navMenu.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!header.contains(e.target)) {
                    navMenu.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>