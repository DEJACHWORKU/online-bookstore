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

    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'], $_POST['rating'])) {
        $book_id = $_POST['book_id'];
        $rating = $_POST['rating'];
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM book_ratings WHERE book_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $book_id, $user_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $stmt = $conn->prepare("INSERT INTO book_ratings (book_id, user_id, rating) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $book_id, $user_id, $rating);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('Thank you for your feedback!');</script>";
        }
    }

    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $books = [];
    
    $query = "SELECT b.*, AVG(r.rating) as avg_rating FROM books b LEFT JOIN book_ratings r ON b.id = r.book_id WHERE 1=1";
    $params = [];
    $types = "";

    if ($search_query) {
        $search_terms = array_filter(array_map('trim', explode(',', $search_query)));
        
        if (!empty($search_terms)) {
            $title_term = $search_terms[0] ?? '';
            $author_term = $search_terms[1] ?? '';
            $dept_term = $search_terms[2] ?? '';
            
            if ($title_term) {
                $query .= " AND b.title LIKE ?";
                $params[] = "%$title_term%";
                $types .= "s";
            }
            if ($author_term) {
                $query .= " AND b.author LIKE ?";
                $params[] = "%$author_term%";
                $types .= "s";
            }
            if ($dept_term) {
                $query .= " AND b.department LIKE ?";
                $params[] = "%$dept_term%";
                $types .= "s";
            }
        }
        
        $query .= " GROUP BY b.id ORDER BY avg_rating DESC LIMIT 1";
    } else {
        $query .= " GROUP BY b.id ORDER BY avg_rating DESC, b.date DESC";
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

    $rated_books = [];
    $rate_stmt = $conn->prepare("SELECT book_id FROM book_ratings WHERE user_id = ?");
    $rate_stmt->bind_param("i", $user_id);
    $rate_stmt->execute();
    $rate_result = $rate_stmt->get_result();
    while ($row = $rate_result->fetch_assoc()) {
        $rated_books[] = $row['book_id'];
    }
    $rate_stmt->close();

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
                            <p class="book-meta"><span class="label">Rating:</span> 
                                <span class="avg-rating"><?php echo $book['avg_rating'] ? number_format($book['avg_rating'], 1) : 'No ratings'; ?></span>
                            </p>
                            <?php if (!in_array($book['id'], $rated_books)): ?>
                            <div class="rating-container">
                                <div class="star-rating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating-<?php echo $book['id']; ?>" id="star<?php echo $i; ?>-<?php echo $book['id']; ?>" value="<?php echo $i; ?>">
                                        <label for="star<?php echo $i; ?>-<?php echo $book['id']; ?>"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <button class="submit-rating" data-book-id="<?php echo $book['id']; ?>">Submit</button>
                            </div>
                            <?php else: ?>
                            <p class="book-meta"><span class="label">Your Rating:</span> Submitted</p>
                            <?php endif; ?>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            const overlay = document.querySelector('.overlay');
            const scrollTopBtn = document.getElementById('scroll-top');
            const logoutBtn = document.querySelector('.logout');
            const searchInput = document.querySelector('.search-input');
            const searchIcon = document.querySelector('.search-icon');

            function toggleMenu() {
                navMenu.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            }

            hamburger.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);

            document.querySelectorAll('.nav-menu a').forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 992) {
                        toggleMenu();
                    }
                });
            });

            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    window.location.href = 'logout.php';
                });
            }

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollTopBtn.classList.add('active');
                } else {
                    scrollTopBtn.classList.remove('active');
                }
            });

            scrollTopBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            function submitSearch() {
                const form = document.createElement('form');
                form.method = 'GET';
                form.style.display = 'none';
                
                if (searchInput.value.trim()) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'search';
                    hiddenField.value = searchInput.value.trim();
                    form.appendChild(hiddenField);
                }
                
                document.body.appendChild(form);
                form.submit();
            }

            searchIcon.addEventListener('click', submitSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') submitSearch();
            });

            document.querySelectorAll('.submit-rating').forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-book-id');
                    const ratingInputs = document.querySelectorAll(`input[name="rating-${bookId}"]:checked`);
                    if (ratingInputs.length > 0) {
                        const rating = ratingInputs[0].value;
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.style.display = 'none';
                        form.innerHTML = `
                            <input type="hidden" name="book_id" value="${bookId}">
                            <input type="hidden" name="rating" value="${rating}">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        alert('Please select a rating before submitting.');
                    }
                });
            });

            function adjustFooterAnimation() {
                const footerText = document.querySelector('.footer-text marquee');
                if (footerText) {
                    const duration = Math.max(20, footerText.scrollWidth / 50);
                    footerText.style.animation = 'none';
                    void footerText.offsetWidth;
                    footerText.style.animation = `slideLeft ${duration}s linear infinite`;
                }
            }

            window.addEventListener('load', adjustFooterAnimation);
            window.addEventListener('resize', adjustFooterAnimation);
        });
    </script>
</body>
</html>