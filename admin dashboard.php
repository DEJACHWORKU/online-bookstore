<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .header {
            background: #2c3e50;
            color: #fff;
            font-size: 1.1rem;
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            line-height: 50px;
        }

        .container {
            display: flex;
            margin-top: 50px;
            transition: all 0.3s ease;
        }

        .sidebar {
            width: 200px;
            background: #2c3e50;
            height: calc(100vh - 50px);
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            padding: 10px 0;
            left: 0;
            z-index: 999;
        }

        .hamburger {
            color: #fff;
            font-size: 1.5rem;
            padding: 10px;
            cursor: pointer;
            background: #2c3e50;
            width: 50px;
            text-align: center;
            position: fixed;
            top: 50px;
            left: 0;
            z-index: 1001;
            display: none; /* Initially hidden */
        }

        .menu {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            border-top: 2px solid #3498db;
            max-height: calc(100vh - 62px);
            overflow-y: auto;
        }

        .menu-item {
            display: block;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
            cursor: pointer;
            margin: 5px 0;
            text-align: left;
            width: 100%;
        }

        .menu-item:hover {
            background: #3498db;
        }

        .menu-item.active {
            background: #2980b9;
            font-weight: bold;
        }

        .menu-item.logout {
            background: #e74c3c;
        }

        .menu-item.logout:hover {
            background: #c0392b;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .content-area {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
            min-height: calc(100vh - 50px);
            transition: all 0.3s ease;
        }

        .content-area.full-width {
            margin-left: 0;
            width: 100%;
        }

        .content-frame {
            width: 100%;
            height: calc(100vh - 70px);
            border: none;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .content-frame.active {
            opacity: 1;
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
            }
            
            .content-area {
                margin-left: 180px;
                width: calc(100% - 180px);
            }
            
            .menu-item {
                padding: 10px 10px;
            }
        }

        @media (max-width: 480px) {
            .header {
                font-size: 1rem;
                line-height: 40px;
            }
            
            .container {
                margin-top: 40px;
            }
            
            .sidebar {
                width: 200px;
            }
        }
    </style>
</head>
<body>
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
                <a href="manage admin.php" class="menu-item">Manage Admin</a>
                <a href="manage librarian.php" class="menu-item">Manage Librarian</a>
                <a href="manage user.php" class="menu-item">Manage User</a>
                <a href="manage book.php" class="menu-item">Manage Book</a>
                <a href="view-comment.php" class="menu-item">View Comment</a>
                <a href="user approval.php" class="menu-item">User Approval</a>
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

            // Initialize sidebar state
            function initSidebar() {
                sidebar.classList.remove('hidden'); // Sidebar visible by default
                hamburger.style.display = 'none';   // Hamburger hidden by default
                contentArea.classList.remove('full-width');
                // Profile is already loaded and marked active in HTML
            }
            initSidebar();

            // Toggle sidebar on hamburger click
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.remove('hidden');
                contentArea.classList.remove('full-width');
                hamburger.style.display = 'none';
            });

            // Handle regular menu item clicks
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
                    
                    // Hide sidebar and show hamburger
                    sidebar.classList.add('hidden');
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                });
            });

            // Handle logout click
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });

            // Close sidebar when clicking outside (only when visible)
            document.addEventListener('click', function(e) {
                if (!sidebar.classList.contains('hidden') && 
                    !sidebar.contains(e.target) && 
                    !hamburger.contains(e.target)) {
                    sidebar.classList.add('hidden');
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (sidebar.classList.contains('hidden')) {
                    contentArea.classList.add('full-width');
                    hamburger.style.display = 'block';
                } else {
                    contentArea.classList.remove('full-width');
                    hamburger.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>