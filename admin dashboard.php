<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin dashboard.css">
</head>
<body>
    <div class="header">
        Welcome to Admin Dashboard - System Over Control Page
    </div>
    <div class="container">
        <div class="sidebar">
            <div class="hamburger">☰</div>
            <div class="menu">
                <a href="#" class="menu-item">Add Admin</a>
                <a href="#" class="menu-item">Add Librarian</a>
                <a href="#" class="menu-item">Add User</a>
                <a href="#" class="menu-item">Add Book</a>
                <a href="#" class="menu-item">Manage Admin</a>
                <a href="#" class="menu-item">Manage Librarian</a>
                <a href="#" class="menu-item">Manage User</a>
                <a href="#" class="menu-item">Manage Book</a>
                <a href="#" class="menu-item">View Comment</a>
                <a href="#" class="menu-item logout">Logout</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');

            if (window.innerWidth <= 480) {
                sidebar.classList.add('hidden');
                hamburger.style.display = 'block';
            } else {
                sidebar.classList.remove('hidden');
                hamburger.style.display = 'none';
            }

            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    sidebar.classList.add('hidden');
                    hamburger.style.display = 'block';
                });
            });

            hamburger.addEventListener('click', function() {
                sidebar.classList.remove('hidden');
                hamburger.style.display = 'none';
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth <= 480) {
                    if (sidebar.classList.contains('hidden')) {
                        hamburger.style.display = 'block';
                    } else {
                        hamburger.style.display = 'none';
                    }
                } else {
                    sidebar.classList.remove('hidden');
                    hamburger.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !hamburger.contains(e.target) && 
                    !sidebar.classList.contains('hidden')) {
                    sidebar.classList.add('hidden');
                    hamburger.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>