<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
        }

        header {
            background: linear-gradient(to right, #2c3e50, #4a627a);
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header h1 {
            color: #fff;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        header h1 i {
            color: #3498db;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            color: #fff;
            font-size: 1.5rem;
            padding: 0.5rem;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 0.8rem;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(255,255,255,0.05);
            white-space: nowrap;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.15);
        }

        .nav-menu a.active {
            background: #3498db;
        }

        .logout {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 0.5rem 0.8rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
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
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        #contentFrame {
            width: 100%;
            height: 100%;
            border: none;
        }

        @media (max-width: 992px) {
            .nav-menu a, .logout {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
                order: -1; /* Move to left side */
                margin-right: auto;
            }

            header h1 {
                margin-left: auto;
                margin-right: auto;
            }

            .nav-menu {
                position: fixed;
                top: 60px;
                left: -280px; /* Start off-screen to the left */
                width: 250px; /* Fixed width for the menu */
                background: #2c3e50;
                flex-direction: column;
                padding: 1rem;
                transition: left 0.3s ease;
                gap: 0.5rem;
                height: calc(100vh - 60px);
                overflow-y: auto;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }

            .nav-menu.active {
                left: 0; /* Slide in from left */
            }

            .nav-menu a, .logout {
                width: 100%;
                padding: 0.8rem 1rem;
                border-radius: 5px;
                text-align: left; /* Left-align text */
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .nav-menu a i, .logout i {
                width: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header id="header">
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <h1><i class="fas fa-book"></i> Librarian Dashboard</h1>
        <nav class="nav-menu">
            <a href="user.php"><i class="fas fa-store"></i> Go to Store</a>
            <a href="#" data-page="dep't.php"><i class="fas fa-building"></i> Add Department</a>
            <a href="#" data-page="add book.php"><i class="fas fa-book-medical"></i> Add Book</a>
            <a href="#" data-page="user/user register form.php"><i class="fas fa-user-plus"></i> Add User</a>
            <a href="#" data-page="view labra profile.php"><i class="fas fa-user-circle"></i> Your Profile</a>
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
                window.location.href = 'index.php';
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