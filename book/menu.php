<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manage page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <header id="header">
        <h1><i class="fas fa-book-reader"></i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="#" data-page="menu/add-user.php">Add User</a>
            <a href="#" data-page="menu/manage-user.php">Manage User</a>
            <a href="#" data-page="menu/manage book.php">Manage Book</a>
            <a href="#" data-page="menu/notify-user.php">Notify Users</a>
            <a href="#" data-page="menu/view-comment.php">View Comment</a>
            <button class="logout">Logout</button>
        </nav>
    </header>
    
    <div class="content-container">
        <div class="iframe-container">
            <iframe id="contentFrame" src="about:blank" frameborder="0"></iframe>
        </div>
    </div>

    <script src="css/menu.js"></script>
</body>
</html>