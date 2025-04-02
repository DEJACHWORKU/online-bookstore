<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page - Online Bookstore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/userpage.css">
</head>
<body class="default">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_Db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $search_title = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
    $search_author = isset($_GET['search_author']) ? trim($_GET['search_author']) : '';
    $search_department = isset($_GET['search_department']) ? trim($_GET['search_department']) : '';
    $books = [];
    
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];
    $types = "";

    if ($search_title) {
        $query .= " AND title LIKE ?";
        $params[] = "%$search_title%";
        $types .= "s";
    }
    if ($search_author) {
        $query .= " AND author LIKE ?";
        $params[] = "%$search_author%";
        $types .= "s";
    }
    if ($search_department) {
        $query .= " AND department LIKE ?";
        $params[] = "%$search_department%";
        $types .= "s";
    }

    $query .= " ORDER BY date DESC";

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            $stmt->close();
        }
    } else {
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    $conn->close();
    ?>

    <header id="header">
        <h1><i class="icon">📚</i> Online Bookstore</h1>
        <div class="hamburger"><i class="fas fa-bars"></i></div>
        <nav class="nav-menu">
            <a href="#store" class="menu-item">Store Page</a>
            <a href="#book-list" class="menu-item">View Book List</a>
            <a href="comment.php" class="menu-item">Comments</a>
            <div class="theme-selector">
                <a href="#" class="theme-toggle">Theme<i class="icon fas fa-paint-brush"></i></a>
                <div class="dropdown">
                    <a href="#" data-theme="default">Default</a>
                    <a href="#" data-theme="light-reader">Light Reader</a>
                    <a href="#" data-theme="sepia-reader">Eye-care Reader</a>
                    <a href="#" data-theme="dark-reader">Dark Reader</a>
                </div>
            </div>
            <button class="logout">Logout<i class="icon">🔒</i></button>
        </nav>
    </header>

    <div class="search-section">
        <div class="search-container">
            <form method="GET" action="" id="search-form">
                <div class="search-fields">
                    <div class="search-input-container">
                        <label class="search-label">Title:</label>
                        <div class="input-wrapper">
                            <input type="text" name="search_title" class="search-input" placeholder="Enter Book Title" value="<?php echo htmlspecialchars($search_title); ?>">
                            <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="search-input-container">
                        <label class="search-label">Author:</label>
                        <div class="input-wrapper">
                            <input type="text" name="search_author" class="search-input" placeholder="Enter Author" value="<?php echo htmlspecialchars($search_author); ?>">
                            <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="search-input-container">
                        <label class="search-label">Department:</label>
                        <div class="input-wrapper">
                            <input type="text" name="search_department" class="search-input" placeholder="Enter Department" value="<?php echo htmlspecialchars($search_department); ?>">
                            <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <main class="book-results">
        <div class="book-grid">
            <?php if (empty($books)): ?>
                <div class="no-books">
                    <i class="fas fa-book-open"></i>
                    <h3>No books found</h3>
                    <p><?php echo ($search_title || $search_author || $search_department) ? "No results found" : "The book collection is empty"; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" class="book-cover">
                        <div class="book-details">
                            <p class="book-meta"><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
                            <p class="book-meta"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-meta"><strong>Department:</strong> <?php echo htmlspecialchars($book['department']); ?></p>
                            <p class="book-meta"><strong>Published:</strong> <?php echo date('M d, Y', strtotime($book['date'])); ?></p>
                            <p class="book-description"><strong>Description:</strong> <?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></p>
                            <div class="book-actions">
                                <?php if ($book['is_read']): ?>
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" target="_blank" class="read-btn">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if ($book['is_download']): ?>
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" download="<?php echo htmlspecialchars($book['title'] . '.pdf'); ?>" class="download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="footer-text">
            WELCOME TO ARSI UNIVERSITY WEB-BASED ONLINE BOOKSTORE SYSTEM DERA THIS BOOKSTORE USERS THIS THE LAST PAGE FOOTER LINE IF YOU NOT FOND YOUR NEED BOOK PLEASE LEAVE COMENT IN THE COMMENT SECTION WE WILL UPLOAD YOUR NEED BOOK OR LEARNING MATERIAL WE GAT NECESSARY HAVE NICE TIME 
        </div>
    </footer>

    <div class="scroll-top" id="scroll-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="js/userpage.js"></script>
</body>
</html>