<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login1.php");
    exit;
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
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'user';

if ($user_type === 'user') {
    $user_stmt = $conn->prepare("SELECT full_name, profile_image FROM users WHERE id = ?");
} elseif ($user_type === 'admin') {
    $user_stmt = $conn->prepare("SELECT full_name, profile_image FROM admin WHERE id = ?");
} elseif ($user_type === 'librarian') {
    $user_stmt = $conn->prepare("SELECT full_name, profile_image FROM librarian WHERE id = ?");
} else {
    $user_stmt = false;
}

$full_name = 'User';
$profile_image = 'https://via.placeholder.com/50';

if ($user_stmt) {
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $full_name = htmlspecialchars($user['full_name'] ?? 'User');
        if ($user_type === 'admin') {
            $base_path = '/bookstore/book/Admin/';
        } elseif ($user_type === 'librarian') {
            $base_path = '/bookstore/book/Librarian/';
        } else {
            $base_path = '/bookstore/book/';
        }
        $image_path = !empty($user['profile_image']) ? $_SERVER['DOCUMENT_ROOT'] . $base_path . $user['profile_image'] : '';
        $profile_image = (!empty($user['profile_image']) && file_exists($image_path)) 
            ? $base_path . htmlspecialchars($user['profile_image']) 
            : 'https://via.placeholder.com/50';
    }
    $user_stmt->close();
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$books = [];

$query = "SELECT b.*, 
                 COALESCE((SELECT AVG(rating) FROM book_ratings WHERE book_id = b.id), 0) as avg_rating,
                 COALESCE((SELECT COUNT(rating) FROM book_ratings WHERE book_id = b.id), 0) as rating_count,
                 (SELECT rating FROM book_ratings WHERE book_id = b.id AND user_id = ?) as user_rating
          FROM books b WHERE 1=1";
$params = [$user_id];
$types = "i";

if ($search_query) {
    $search_terms = array_filter(array_map('trim', explode(',', $search_query)));
    if (!empty($search_terms)) {
        $query .= " AND (";
        $first = true;
        foreach ($search_terms as $index => $term) {
            if (!$first) $query .= " OR ";
            $query .= "(title LIKE ? OR author LIKE ? OR department LIKE ? 
                      OR SOUNDEX(title) = SOUNDEX(?) 
                      OR SOUNDEX(author) = SOUNDEX(?) 
                      OR SOUNDEX(department) = SOUNDEX(?))";
            $params[] = "%$term%";
            $params[] = "%$term%";
            $params[] = "%$term%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $types .= "ssssss";
            $first = false;
        }
        $query .= ")";
    }
}

$query .= " ORDER BY avg_rating DESC, rating_count DESC, date DESC";

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
                <?php if ($user_type === 'user'): ?>
                    <a href="user profile.php">Details</a>
                <?php endif; ?>
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
            <div class="search-input-container">
                <label for="search-input" class="search-label">Search by:</label>
                <input type="text" name="search" id="search-input" class="search-input" placeholder="Search by title, author, or department" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="button" class="clear-search" title="Clear Search"><i class="fas fa-sync-alt"></i></button>
                <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>

    <main id="book-list">
        <div class="book-grid">
            <?php if (empty($books) && $search_query): ?>
                <div class="no-books">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Book Found</h3>
                    <p>No book matches your search criteria.</p>
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
                            <p class="book-description"><span class="label">Description:</span> <?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></p>
                            <div class="book-rating">
                                <span class="label">Rating:</span>
                                <span class="average-rating"><?php echo ($book['avg_rating'] > 0) ? round($book['avg_rating'], 1) : '0.0'; ?></span>
                                <span class="rating-count">(<?php echo $book['rating_count']; ?> ratings)</span>
                                <?php if (is_null($book['user_rating'])): ?>
                                    <div class="star-rating" data-book-id="<?php echo $book['id']; ?>">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" data-value="<?php echo $i; ?>"></i>
                                        <?php endfor; ?>
                                        <button class="submit-rating" disabled>Submit Rating</button>
                                    </div>
                                <?php else: ?>
                                    <div class="user-rating">Your rating: <?php echo $book['user_rating']; ?> stars</div>
                                <?php endif; ?>
                            </div>
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
                                       rel="noopener noreferrer">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>
                                <?php if ($file_exists && isset($book['is_download']) && $book['is_download']): ?>
                                    <a href="download_book.php?file=<?php echo rawurlencode($book['file']); ?>&title=<?php echo rawurlencode($book['title']); ?>" 
                                       class="download-btn">
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
            <marquee behavior="" direction="left">WELCOME TO ARSI UNIVERSITY WEB-BASED ONLINE BOOKSTORE SYSTEM</marquee>    
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
            const clearSearchBtn = document.querySelector('.clear-search');

            function toggleMenu() {
                navMenu.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            }

            hamburger.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);

            document.querySelectorAll('.nav-menu a').forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleMenu();
                    }
                });
            });

            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    fetch('logout.php', {
                        method: 'POST'
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.href = 'index.php';
                        }
                    });
                });
            }

            window.addEventListener('scroll', function() {
                scrollTopBtn.classList.toggle('active', window.pageYOffset > 300);
            });

            scrollTopBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            function submitSearch() {
                const searchValue = searchInput.value.trim();
                window.location.href = window.location.pathname + (searchValue ? '?search=' + encodeURIComponent(searchValue) : '');
            }

            function clearSearch() {
                searchInput.value = '';
                window.location.href = window.location.pathname;
            }

            searchIcon.addEventListener('click', submitSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') submitSearch();
            });

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', clearSearch);
            }

            document.querySelectorAll('.star-rating').forEach(ratingContainer => {
                const bookId = ratingContainer.getAttribute('data-book-id');
                const stars = ratingContainer.querySelectorAll('.fa-star');
                const submitBtn = ratingContainer.querySelector('.submit-rating');
                let selectedRating = 0;

                stars.forEach(star => {
                    star.addEventListener('mouseover', () => {
                        const value = parseInt(star.getAttribute('data-value'));
                        stars.forEach(s => {
                            s.classList.toggle('hover', parseInt(s.getAttribute('data-value')) <= value);
                        });
                    });

                    star.addEventListener('mouseout', () => {
                        stars.forEach(s => {
                            s.classList.remove('hover');
                            s.classList.toggle('selected', selectedRating > 0 && parseInt(s.getAttribute('data-value')) <= selectedRating);
                        });
                    });

                    star.addEventListener('click', () => {
                        selectedRating = parseInt(star.getAttribute('data-value'));
                        stars.forEach(s => {
                            s.classList.remove('selected', 'hover');
                            s.classList.toggle('selected', parseInt(s.getAttribute('data-value')) <= selectedRating);
                        });
                        submitBtn.disabled = false;
                    });
                });

                submitBtn.addEventListener('click', () => {
                    if (selectedRating > 0) {
                        fetch('submit_rating.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `book_id=${bookId}&rating=${selectedRating}`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const ratingContainer = document.querySelector(`.star-rating[data-book-id="${bookId}"]`).parentElement;
                                const currentCount = parseInt(ratingContainer.querySelector('.rating-count').textContent.match(/\d+/)[0]);
                                const newCount = currentCount + 1;
                                const newAvgRating = parseFloat(data.avg_rating).toFixed(1);
                                ratingContainer.innerHTML = `
                                    <span class="label">Rating:</span>
                                    <span class="average-rating">${newAvgRating > 0 ? newAvgRating : '0.0'}</span>
                                    <span class="rating-count">(${newCount} ratings)</span>
                                    <div class="user-rating">Your rating: ${selectedRating} stars</div>
                                `;
                                const feedback = document.createElement('div');
                                feedback.className = 'feedback-message';
                                feedback.textContent = 'Thank you for your feedback!';
                                document.body.appendChild(feedback);
                                setTimeout(() => feedback.remove(), 3000);
                            } else {
                                alert(data.message || 'Error submitting rating.');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('An error occurred while submitting your rating.');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>