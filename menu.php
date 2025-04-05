<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Styles - Embedded for complete solution */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            transition: all 0.3s ease;
        }

        body.menu-open {
            overflow: hidden;
        }

        #header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        #header h1 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Hamburger icon */
        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            padding: 0.5rem;
            color: white;
            margin-left: auto; /* Push to right side */
        }

        /* Navigation menu */
        .nav-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-size: 1rem;
        }

        .nav-menu a:hover, .nav-menu a.active {
            background-color: #34495e;
        }

        .nav-menu .logout {
            background-color: #e74c3c;
            border: none;
            color: white;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .nav-menu .logout:hover {
            background-color: #c0392b;
        }

        /* Content area */
        .content-container {
            padding: 1rem;
        }

        .iframe-container {
            width: 100%;
            height: calc(100vh - 70px);
            position: relative;
        }

        .iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            transition: opacity 0.3s;
            opacity: 0;
        }

        .iframe-container iframe.loaded {
            opacity: 1;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 250px;
                height: auto; /* Auto height based on content */
                background-color: #2c3e50;
                flex-direction: column;
                align-items: stretch;
                padding: 0;
                gap: 0;
                transition: left 0.3s ease;
                overflow-y: auto;
                box-shadow: 2px 0 5px rgba(0,0,0,0.2);
            }
            
            .nav-menu.active {
                left: 0;
            }
            
            .nav-menu a, .nav-menu .logout {
                width: 100%;
                text-align: left;
                padding: 0.75rem 1rem;
                box-sizing: border-box;
                margin: 0;
                font-size: 1rem;
                border-radius: 0;
                display: block;
                border-bottom: 1px solid #34495e;
            }
            
            .nav-menu a:hover, .nav-menu a.active {
                background-color: #34495e;
            }
            
            .nav-menu .logout {
                margin-top: 0;
                text-align: left;
                background-color: #e74c3c;
                border-radius: 0;
                border-bottom: none;
            }
            
            .nav-menu .logout:hover {
                background-color: #c0392b;
            }
            
            #header {
                justify-content: space-between;
            }
            
            #header h1 {
                margin-left: 0;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <header id="header">
        <h1><i class="fas fa-book-reader"></i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="librarian.php">Go back</a>
            <a href="#" data-page="admin register form.php">Manage User</a>
            <a href="#" data-page="manage book.php" class="active">Manage Book</a>
            <a href="#" data-page="notify-user.php">Notify Users</a>
            <a href="#" data-page="view-comment.php">View Comment</a>
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
        });
    </script>
</body>
</html>