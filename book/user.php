<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 <link rel="stylesheet" href="css/userpage.css">
</head>
<body>
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
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
    }
    $conn->close();
    ?>

    <header id="header">
        <h1><i class="icon">📚</i> Online Bookstore</h1>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="#store">Store page<i class="icon">🛒</i></a>
            <a href="#book-list">View Book List<i class="icon">📖</i></a>
            <a href="comment.php">Comments<i class="icon">💬</i></a>
            <button class="logout">Logout<i class="icon">🔒</i></button>
            
        </nav>
        <div class="overlay"></div>
    </header>

    <div class="search-section">
    <div class="search-container">
        <div class="search-fields">
            <div class="search-input-container">
                <div class="input-wrapper">
                    <span class="search-label">Search By:</span>
                    <input type="text" name="search_title" class="search-input" placeholder="Enter Book title" value="<?php echo htmlspecialchars($search_title); ?>">
                    <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="search-input-container">
                <div class="input-wrapper">
                    <span class="search-label">Search By:</span>
                    <input type="text" name="search_author" class="search-input" placeholder="Enter Author name" value="<?php echo htmlspecialchars($search_author); ?>">
                    <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="search-input-container">
                <div class="input-wrapper">
                    <span class="search-label">Search By:</span>
                    <input type="text" name="search_department" class="search-input" placeholder="Enter Department" value="<?php echo htmlspecialchars($search_department); ?>">
                    <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
    <main class="reading-optimized" id="book-list">
        <div class="book-grid">
            <?php if (empty($books)): ?>
                <div class="no-books">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Books Found</h3>
                    <p><?php echo ($search_title || $search_author || $search_department) ? "No results match your search." : "The book collection is empty."; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" class="book-cover">
                        <div class="book-details">
                            <p class="book-meta"><span class="label">Title:</span> <?php echo htmlspecialchars($book['title']); ?></p>
                            <p class="book-meta"><span class="label">Author:</span> <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-meta"><span class="label">Department:</span> <?php echo htmlspecialchars($book['department']); ?></p>
                            <p class="book-meta"><span class="label">Published:</span> <?php echo date('M d, Y', strtotime($book['date'])); ?></p>
                            <p class="book-description"><span class="label">Book Description:</span> <?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></p>
                            <div class="book-actions">
                                <?php if (isset($book['is_read']) && $book['is_read']): ?>
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" target="_blank" class="read-btn" rel="noopener noreferrer">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if (isset($book['is_download']) && $book['is_download']): ?>
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
           <marquee behavior="" direction="left">WELCOME TO ARSI UNIVERSITY WEB-BASED ONLINE BOOKSTORE SYSTEM DERA THIS BOOKSTORE USERS  IF YOU NOT FOND YOUR NEED BOOK PLEASE LEAVE COMENT IN THE COMMENT SECTION WE WILL UPLOAD YOUR NEED BOOK OR LEARNING MATERIAL WE GAT NECESSARY HAVE NICE TIME </marquee>    
        </div>
    </footer>
    <div class="scroll-top" id="scroll-top">
        <i class="fas fa-arrow-up"></i>
    </div>
  <script src="css/userpage.js"></script>
</body>
</html>