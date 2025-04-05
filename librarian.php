<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/librarian.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #f4f7fa;
            min-height: 100vh;
            color: #333;
        }

        header {
            background: #2c3e50;
            padding:1.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header h1 {
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            color: white;
            font-size: 1.2rem;
            padding: 0.3rem;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 1rem;
            font-weight: 500;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-menu a.active {
            background: #3498db;
            color: white;
        }

        .logout {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .logout:hover {
            background: #c0392b;
        }

        .content-container {
            margin-top: 60px;
            padding: 1rem;
            height: calc(100vh - 60px);
        }

        .iframe-container {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        #contentFrame {
            width: 100%;
            height: 100%;
            border: none;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-menu {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 200px;
                background: #2c3e50;
                flex-direction: column;
                align-items: stretch;
                padding: 0.5rem 0;
                gap: 0;
                transition: left 0.3s ease;
                box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            }

            .nav-menu.active {
                left: 0;
            }

            .nav-menu a, .logout {
                width: 100%;
                padding: 0.6rem 1rem;
                margin: 0;
                border-radius: 0;
                text-align: left;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .logout {
                margin-top: 0.5rem;
                border-bottom: none;
            }
        }
    </style>
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
            <a href="#" data-page="add author.php">Add Author</a>
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