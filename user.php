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

    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $books = [];
    
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];
    $types = "";

    if ($search_query) {
        $search_terms = array_filter(array_map('trim', explode(',', $search_query)));
        
        if (!empty($search_terms)) {
            $title_term = $search_terms[0] ?? '';
            $author_term = $search_terms[1] ?? '';
            $dept_term = $search_terms[2] ?? '';
            
            if ($title_term) {
                $query .= " AND title LIKE ?";
                $params[] = "%$title_term%";
                $types .= "s";
            }
            if ($author_term) {
                $query .= " AND author LIKE ?";
                $params[] = "%$author_term%";
                $types .= "s";
            }
            if ($dept_term) {
                $query .= " AND department LIKE ?";
                $params[] = "%$dept_term%";
                $types .= "s";
            }
        }
        
        $query .= " ORDER BY date DESC LIMIT 1";
    } else {
   
        $query .= " ORDER BY date DESC";
    }

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
<a href="index.php">Go home <i class="fas fa-home"></i></a>
<a href="view_book_list.php">Go Book List <i class="fas fa-book-open"></i></a>
<a href="comment.php">Comments <i class="fas fa-comments"></i></a>
<a href="profile.php">Your profile <i class="fas fa-user-circle"></i></a>
<button class="logout">Logout <i class="fas fa-sign-out-alt"></i></button>
        </nav>
        <div class="overlay"></div>
    </header>

    <div class="search-section">
        <div class="search-container">
            <div class="search-fields">
                <div class="search-input-container">
                    <div class="input-wrapper">
                        <span class="search-label">Search:</span>
                        <input type="text" name="search" class="search-input" placeholder="title, author, department" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="reading-optimized" id="book-list">
        <div class="book-grid">
            <?php if (empty($books) && $search_query): ?>
                <div class="no-books">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Book Found</h3>
                    <p>No book matches all your search criteria.</p>
                </div>
            <?php elseif (empty($books) && !$search_query): ?>
                <div class="no-books">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Books Available</h3>
                    <p>There are currently no books in the database.</p>
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
            <marquee behavior="" direction="left">WELCOME TO ARSI UNIVERSITY WEB-BASED ONLINE BOOKSTORE SYSTEM DERA THIS BOOKSTORE USERS  IF YOU NOT FOND YOUR NEED BOOK PLEASE LEAVE COMENT IN THE COMMENT SECTION WE WILL UPLOAD YOUR NEED BOOK OR LEARNING MATERIAL WE GAT NECESSARY HAVE NICE TIME</marquee>    
        </div>
    </footer>
    <div class="scroll-top" id="scroll-top">
        <i class="fas fa-arrow-up"></i>
    </div>
    <script src="css/userpage.js"></script>
</body>
</html>