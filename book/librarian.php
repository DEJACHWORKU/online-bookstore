<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librerian page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/librarian.css">
    
</head>
<body class="default">
    <header id="header">
        <h1><i class="icon">📚</i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
        <a href="user.php">Go to store</a>
            <a href="#" data-page="dep't.php">Add Department</a>
            <a href="#" data-page="add author.php">Add author</a>
            <a href="#" data-page="add book.php">Add Book</a>
            <a href="menu.php">Go to manage</a>
            <button class="logout">Logout</button>
           
        </nav>
    </header>
    
    <div class="content-container">
        <div class="iframe-container">
            <iframe id="contentFrame" src="about:blank" frameborder="0" class="hidden"></iframe>
        </div>
    </div>

    <script src="css/librarian.js"></script>

</body>
</html>