<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="default">
    <header id="header">
        <h1><i class="icon">📚</i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="#store">Store Page<i class="icon">🛒</i></a>
            <a href="#book-list">View Book List<i class="icon">📖</i></a>
            <a href="#comment">Comment<i class="icon">💬</i></a>
            <div class="theme-selector">
                <a href="#" class="theme-toggle">Theme<i class="icon fas fa-paint-brush"></i></a>
                <div class="dropdown">
                    <a href="#" data-theme="default">Default</a>
                    <a href="#" data-theme="light-reader">Light Reader<span class="icon-container">
                        <span class="icon-circle minus-circle"><i class="fas fa-minus"></i></span>
                        <span class="icon-circle plus-circle"><i class="fas fa-plus"></i></span>
                    </span></a>
                    <a href="#" data-theme="sepia-reader">Sepia Reader<span class="icon-container">
                        <span class="icon-circle minus-circle"><i class="fas fa-minus"></i></span>
                        <span class="icon-circle plus-circle"><i class="fas fa-plus"></i></span>
                    </span></a>
                    <a href="#" data-theme="dark-reader">Dark Reader<span class="icon-container">
                        <span class="icon-circle minus-circle"><i class="fas fa-minus"></i></span>
                        <span class="icon-circle plus-circle"><i class="fas fa-plus"></i></span>
                    </span></a>
                </div>
            </div>
            <button class="logout">Logout<i class="icon">🔒</i></button>
            <i class="fas fa-arrow-down scroll-down" id="scroll-down"></i>
        </nav>
    </header>

    <main class="reading-optimized">
        <!-- Content will go here -->
    </main>

    <footer>
        <div class="footer-text">
            WELCOME TO ARSI UNIVERSITY WEB-BASED ONLINE BOOKSTORE SYSTEM DERA THIS BOOKSTORE USERS THIS THE LAST PAGE FOOTER LINE IF YOU NOT FOND YOUR NEED BOOK PLEASE LEAVE COMENT IN THE COMMENT SECTION WE WILL UPLOAD YOUR NEED BOOK OR LEARNING MATERIAL WE GAT NECESSARY HAVE NICE TIME 
        </div>
    </footer>

    <div class="scroll-top" id="scroll-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="css/index.js"></script>
</body>
</html>