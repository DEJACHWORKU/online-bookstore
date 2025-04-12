<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$user_stmt = $conn->prepare("SELECT full_name, profile_image FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$full_name = $user ? htmlspecialchars($user['full_name']) : 'User';
$profile_image = !empty($user['profile_image']) ? '/bookstore/book/' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/50';
$user_stmt->close();

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
            $query .= " AND (title LIKE ? OR SOUNDEX(title) = SOUNDEX(?))";
            $params[] = "%$title_term%";
            $params[] = $title_term;
            $types .= "ss";
        }
        if ($author_term) {
            $query .= " AND (author LIKE ? OR SOUNDEX(author) = SOUNDEX(?))";
            $params[] = "%$author_term%";
            $params[] = $author_term;
            $types .= "ss";
        }
        if ($dept_term) {
            $query .= " AND (department LIKE ? OR SOUNDEX(department) = SOUNDEX(?))";
            $params[] = "%$dept_term%";
            $params[] = $dept_term;
            $types .= "ss";
        }
    }
    
    $query .= " ORDER BY date DESC";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/userpage.css">
</head>
<body>
    <header id="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="<?php echo $profile_image; ?>" alt="User Profile">
            </div>
            <div class="user-info">
                <span><?php echo $full_name; ?></span>
                <a href="profile.php">Details</a>
            </div>
        </div>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="nav-menu">
            <a href="index.php">Go home <i class="fas fa-home"></i></a>
            <a href="view_book_list.php">Go Book List <i class="fas fa-book-open"></i></a>
            <a href="comment.php">Comments <i class="fas fa-comments"></i></a>
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
                                <?php 
                                $file_path = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $book['file'];
                                $file_exists = !empty($book['file']) && file_exists($file_path);
                                $file_extension = !empty($book['file']) ? strtolower(pathinfo($book['file'], PATHINFO_EXTENSION)) : '';
                                $is_pdf = $file_extension === 'pdf';
                                ?>
                                <?php if ($file_exists && isset($book['is_read']) && $book['is_read'] && $is_pdf): ?>
                                    <a href="read_book.php?file=<?php echo rawurlencode($book['file']); ?>" 
                                       target="_blank" 
                                       class="read-btn" 
                                       rel="noopener noreferrer"
                                       onclick="if (!confirm('Opening book for reading...')) return false;">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if ($file_exists && isset($book['is_download']) && $book['is_download']): ?>
                                    <a href="download_book.php?file=<?php echo rawurlencode($book['file']); ?>&title=<?php echo rawurlencode($book['title']); ?>" 
                                       class="download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                                <?php if (!$file_exists || ($book['is_read'] && !$is_pdf)): ?>
                                    <a href="#" 
                                       class="read-btn disabled" 
                                       onclick="alert('Error: The file is not available or not a PDF. Only PDFs can be read. Check the file in /bookstore/book/.'); return false;">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if (!$file_exists): ?>
                                    <a href="#" 
                                       class="download-btn disabled" 
                                       onclick="alert('Error: The file is not available for download. Check the file in /bookstore/book/.'); return false;">
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
                const searchValue = searchInput.value.trim();
                const form = document.createElement('form');
                form.method = 'GET';
                form.style.display = 'none';
                
                if (searchValue) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'search';
                    hiddenField.value = searchValue;
                    form.appendChild(hiddenField);
                }
                
                document.body.appendChild(form);
                form.submit();
            }

            searchIcon.addEventListener('click', submitSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') submitSearch();
            });

            searchInput.addEventListener('input', function() {
                if (!this.value.trim()) {
                    window.location.href = window.location.pathname;
                }
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