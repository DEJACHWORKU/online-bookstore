<?php
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '21M');
ini_set('max_execution_time', '300');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_book'])) {
        $book_date = $_POST['book_date'] ?? '';
        $book_title = trim($_POST['book_title'] ?? '');
        $book_description = trim($_POST['book_description'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $is_read = isset($_POST['readCheckbox']) ? 1 : 0;
        $is_download = isset($_POST['downloadCheckbox']) ? 1 : 0;
        $book_cover = $_FILES['book_cover'];
        $book_file = $_FILES['book_file'];

        $errors = [];
        if (empty($book_date)) $errors[] = "Please select a date.";
        if (empty($book_title)) $errors[] = "Book title is required.";
        if (empty($book_description)) $errors[] = "Book description is required.";
        if (empty($department)) $errors[] = "Department is required.";
        if (empty($author)) $errors[] = "Author is required.";

        if ($book_cover['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading book cover.";
        } else {
            $cover_ext = strtolower(pathinfo($book_cover['name'], PATHINFO_EXTENSION));
            if (!in_array($cover_ext, ['png', 'jpg', 'jpeg'])) {
                $errors[] = "Invalid book cover format! Please upload a PNG or JPEG image.";
            }
            if ($book_cover['size'] > 20971520) {
                $errors[] = "Book cover must be less than 20MB";
            }
        }

        if ($book_file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading book file.";
        } elseif ($book_file['type'] !== 'application/pdf') {
            $errors[] = "Invalid file format! Please upload a PDF file.";
        } elseif ($book_file['size'] > 20971520) {
            $errors[] = "Book file must be less than 20MB";
        }

        if (empty($errors)) {
            $check_stmt = $conn->prepare("SELECT id FROM books WHERE title = ? AND author = ?");
            $check_stmt->bind_param("ss", $book_title, $author);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $errors[] = "A book with the same title and author already exists.";
            }
            $check_stmt->close();
        }

        if (empty($errors)) {
            $cover_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/uploads/covers/';
            $file_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/uploads/files/';
            $cover_web_path = 'Uploads/covers/';
            $file_web_path = 'Uploads/files/';

            if (!is_dir($cover_dir)) mkdir($cover_dir, 0777, true);
            if (!is_dir($file_dir)) mkdir($file_dir, 0777, true);

            $cover_filename = uniqid() . '.' . $cover_ext;
            $file_filename = uniqid() . '.pdf';
            
            $cover_path = $cover_web_path . $cover_filename;
            $file_path = $file_web_path . $file_filename;

            if (move_uploaded_file($book_cover['tmp_name'], $cover_dir . $cover_filename) && 
                move_uploaded_file($book_file['tmp_name'], $file_dir . $file_filename)) {
                
                $stmt = $conn->prepare("INSERT INTO books (date, title, description, department, author, cover, file, is_read, is_download) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssii", $book_date, $book_title, $book_description, $department, $author, $cover_path, $file_path, $is_read, $is_download);
                
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'New book added successfully!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error uploading files!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        }
        exit;
    }
    elseif (isset($_POST['edit_book'])) {
        $edit_id = $_POST['edit_id'];
        $book_date = $_POST['book_date'];
        $book_title = trim($_POST['book_title']);
        $book_description = trim($_POST['book_description']);
        $department = trim($_POST['department']);
        $author = trim($_POST['author']);
        $is_read = isset($_POST['readCheckbox']) ? 1 : 0;
        $is_download = isset($_POST['downloadCheckbox']) ? 1 : 0;
        $existing_cover = $_POST['existing_cover'];
        $existing_file = $_POST['existing_file'];
        
        $cover_path = $existing_cover;
        $file_path = $existing_file;
        $cover_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/uploads/covers/';
        $file_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/uploads/files/';
        
        $errors = [];
        
        if (!empty($_FILES['book_cover']['name'])) {
            $cover_ext = strtolower(pathinfo($_FILES['book_cover']['name'], PATHINFO_EXTENSION));
            if (!in_array($cover_ext, ['png', 'jpg', 'jpeg'])) {
                $errors[] = "Invalid book cover format! Please upload a PNG or JPEG image.";
            }
            if ($_FILES['book_cover']['size'] > 20971520) {
                $errors[] = "Book cover must be less than 20MB";
            }
            
            if (empty($errors)) {
                $cover_filename = uniqid() . '.' . $cover_ext;
                $cover_path = 'Uploads/covers/' . $cover_filename;
                
                if (move_uploaded_file($_FILES['book_cover']['tmp_name'], $cover_dir . $cover_filename)) {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $existing_cover)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $existing_cover);
                    }
                } else {
                    $cover_path = $existing_cover;
                }
            }
        }
        
        if (!empty($_FILES['book_file']['name'])) {
            if ($_FILES['book_file']['type'] !== 'application/pdf') {
                $errors[] = "Invalid file format! Please upload a PDF file.";
            }
            if ($_FILES['book_file']['size'] > 20971520) {
                $errors[] = "Book file must be less than 20MB";
            }
            
            if (empty($errors)) {
                $file_filename = uniqid() . '.pdf';
                $file_path = 'Uploads/files/' . $file_filename;
                
                if (move_uploaded_file($_FILES['book_file']['tmp_name'], $file_dir . $file_filename)) {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $existing_file)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $existing_file);
                    }
                } else {
                    $file_path = $existing_file;
                }
            }
        }
        
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE books SET date=?, title=?, description=?, department=?, author=?, cover=?, file=?, is_read=?, is_download=? WHERE id=?");
            $stmt->bind_param("sssssssiii", $book_date, $book_title, $book_description, $department, $author, $cover_path, $file_path, $is_read, $is_download, $edit_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Book updated successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error updating book: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        }
        exit;
    }
    elseif (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $cover_path = $_POST['cover_path'] ?? '';
        $file_path = $_POST['file_path'] ?? '';
        
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            if (!empty($cover_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $cover_path)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $cover_path);
            }
            if (!empty($file_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $file_path)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $file_path);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Book deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting book: ' . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

$search_query = "";
$books = [];

function fuzzyMatch($string1, $string2) {
    $string1 = strtolower($string1);
    $string2 = strtolower($string2);
    $len1 = strlen($string1);
    $len2 = strlen($string2);
    
    if ($len1 == 0 || $len2 == 0) return 0;
    
    $distance = levenshtein($string1, $string2);
    $maxLen = max($len1, $len2);
    return (1 - $distance / $maxLen) * 100;
}

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $search_terms = preg_split('/[\s,]+/', $search_query, -1, PREG_SPLIT_NO_EMPTY);
    
    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];
    $types = "";
    
    foreach ($search_terms as $term) {
        $term = "%$term%";
        $sql .= " AND (title LIKE ? OR author LIKE ? OR department LIKE ?)";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $types .= "sss";
    }
    $sql .= " ORDER BY date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $temp_books = [];
    while ($row = $result->fetch_assoc()) {
        $max_similarity = 0;
        foreach ($search_terms as $term) {
            $term = trim($term, '%');
            $title_sim = fuzzyMatch($term, $row['title']);
            $author_sim = fuzzyMatch($term, $row['author']);
            $dept_sim = fuzzyMatch($term, $row['department']);
            $max_similarity = max($max_similarity, $title_sim, $author_sim, $dept_sim);
        }
        $row['similarity'] = $max_similarity;
        $temp_books[] = $row;
    }
    
    usort($temp_books, function($a, $b) {
        return $b['similarity'] <=> $a['similarity'];
    });
    
    $books = array_filter($temp_books, function($book) {
        return $book['similarity'] >= 50;
    });
    
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM books ORDER BY date DESC");
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    <link rel="stylesheet" href="css/fetch.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/themes.css">
</head>
<body>
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <form method="GET" action="" class="d-flex" id="searchForm">
                        <input type="text" class="form-control me-2" name="search" id="searchInput" placeholder="Search books (title, author, department - use comma or space)" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-primary" id="refreshBooks">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fas fa-plus me-2"></i> Add Book
                    </button>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div id="messageContainer"></div>

        <div class="row" id="bookContainer">
            <?php if (empty($books)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-book-open fa-3x mb-3"></i>
                        <h4>No books found</h4>
                        <p><?php echo $search_query ? "No results for '$search_query'" : "The book collection is empty"; ?></p>
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBookModal">
                            <i class="fas fa-plus me-2"></i> Add Your First Book
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-lg-4 col-md-6 mb-4 book-card">
                        <div class="card h-100">
                            <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <span>Title:</span>
                                    <span><?php echo htmlspecialchars($book['title']); ?></span>
                                </h5>
                                <p class="card-text">
                                    <span>Description:</span>
                                    <span><?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></span>
                                </p>
                                <div>
                                    <?php if ($book['is_read']): ?>
                                        <span class="status-badge readable">Readable</span>
                                    <?php endif; ?>
                                    <?php if ($book['is_download']): ?>
                                        <span class="status-badge downloadable">Downloadable</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span>Department:</span>
                                    <span><?php echo htmlspecialchars($book['department']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0">
                                        <span>Author:</span>
                                        <span><?php echo htmlspecialchars($book['author']); ?></span>
                                    </p>
                                    <small>
                                        <span>Date:</span>
                                        <span><?php echo date('M d, Y', strtotime($book['date'])); ?></span>
                                    </small>
                                </div>
                                <div class="d-flex flex-wrap">
                                    <a href="/bookstore/book/<?php echo htmlspecialchars($book['file']); ?>" target="_blank" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editBookModal" 
                                            data-id="<?php echo $book['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                            data-description="<?php echo htmlspecialchars($book['description']); ?>"
                                            data-date="<?php echo htmlspecialchars($book['date']); ?>"
                                            data-department="<?php echo htmlspecialchars($book['department']); ?>"
                                            data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                            data-isread="<?php echo $book['is_read']; ?>"
                                            data-isdownload="<?php echo $book['is_download']; ?>"
                                            data-cover="<?php echo htmlspecialchars($book['cover']); ?>"
                                            data-file="<?php echo htmlspecialchars($book['file']); ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <a href="#" class="btn btn-sm btn-danger delete-book" 
                                       data-id="<?php echo $book['id']; ?>"
                                       data-cover="<?php echo htmlspecialchars($book['cover']); ?>"
                                       data-file="<?php echo htmlspecialchars($book['file']); ?>">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBookForm" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="add_book_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="add_book_date" name="book_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="add_book_title" class="form-label">Book Title</label>
                                <input type="text" class="form-control" id="add_book_title" name="book_title" placeholder="Enter book title" required>
                            </div>
                            <div class="col-12">
                                <label for="add_book_description" class="form-label">Book Description</label>
                                <textarea class="form-control" id="add_book_description" name="book_description" rows="3" placeholder="Enter book description" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="add_department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="add_department" name="department" list="department_list" placeholder="Type or select department" required>
                                <datalist id="department_list">
                                    <?php 
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    $dept_result = $conn->query("SELECT name FROM categories");
                                    while ($row = $dept_result->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['name']); ?>">
                                    <?php endwhile; 
                                    $conn->close();
                                    ?>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label for="add_author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="add_author" name="author" list="author_list" placeholder="Type or select author" required>
                                <datalist id="author_list">
                                    <?php 
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    $author_result = $conn->query("SELECT name FROM authors");
                                    while ($row = $author_result->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['name']); ?>">
                                    <?php endwhile; 
                                    $conn->close();
                                    ?>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label for="add_book_cover" class="form-label">Book Cover (PNG/JPG, max 20MB)</label>
                                <input class="form-control" type="file" id="add_book_cover" name="book_cover" accept="image/png, image/jpeg" required data-max-size="20971520">
                                <small class="text-muted">Maximum file size: 20MB</small>
                            </div>
                            <div class="col-md-6">
                                <label for="add_book_file" class="form-label">Upload File (PDF, max 20MB)</label>
                                <input class="form-control" type="file" id="add_book_file" name="book_file" accept=".pdf" required data-max-size="20971520">
                                <small class="text-muted">Maximum file size: 20MB</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="add_readCheckbox" name="readCheckbox" value="1">
                                    <label class="form-check-label" for="add_readCheckbox">Available for Reading</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="add_downloadCheckbox" name="downloadCheckbox" value="1">
                                    <label class="form-check-label" for="add_downloadCheckbox">Available for Download</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_book" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBookForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="hidden" name="existing_cover" id="existing_cover">
                    <input type="hidden" name="existing_file" id="existing_file">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_book_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="edit_book_date" name="book_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_book_title" class="form-label">Book Title</label>
                                <input type="text" class="form-control" id="edit_book_title" name="book_title" placeholder="Enter book title" required>
                            </div>
                            <div class="col-12">
                                <label for="edit_book_description" class="form-label">Book Description</label>
                                <textarea class="form-control" id="edit_book_description" name="book_description" rows="3" placeholder="Enter book description" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="edit_department" name="department" list="department_list" placeholder="Type or select department" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="edit_author" name="author" list="author_list" placeholder="Type or select author" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_book_cover" class="form-label">Book Cover (PNG/JPG, max 20MB)</label>
                                <input class="form-control" type="file" id="edit_book_cover" name="book_cover" accept="image/png, image/jpeg" data-max-size="20971520">
                                <small class="text-muted">Maximum file size: 20MB</small>
                                <div class="mt-2">
                                    <img id="current_cover" src="" alt="Current Cover" style="max-height: 100px; display: none;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_book_file" class="form-label">Upload File (PDF, max 20MB)</label>
                                <input class="form-control" type="file" id="edit_book_file" name="book_file" accept=".pdf" data-max-size="20971520">
                                <small class="text-muted">Maximum file size: 20MB</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="edit_readCheckbox" name="readCheckbox" value="1">
                                    <label class="form-check-label" for="edit_readCheckbox">Available for Reading</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_downloadCheckbox" name="downloadCheckbox" value="1">
                                    <label class="form-check-label" for="edit_downloadCheckbox">Available for Download</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_book" class="btn btn-warning text-white">
                            <i class="fas fa-save me-2"></i> Update Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme initialization
        const savedTheme = localStorage.getItem('bookstoreTheme');
        if (savedTheme) {
            document.body.className = savedTheme;
        }

        // Theme switcher toggle
        const settingsToggle = document.getElementById('settings-toggle');
        const themeOptions = document.getElementById('theme-options');
        settingsToggle.addEventListener('click', function(e) {
            e.preventDefault();
            themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
        });

        // Theme selection
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                document.body.className = theme;
                localStorage.setItem('bookstoreTheme', theme);
                themeOptions.style.display = 'none';
            });
        });

        // Close theme options when clicking outside
        document.addEventListener('click', function(e) {
            if (!settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                themeOptions.style.display = 'none';
            }
        });

        // Existing functionality
        const messageContainer = document.getElementById('messageContainer');
        const searchInput = document.getElementById('searchInput');
        const bookCards = document.querySelectorAll('.book-card');
        const refreshBooksBtn = document.getElementById('refreshBooks');
        let debounceTimer;

        function showMessage(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            messageContainer.appendChild(alertDiv);
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }, 5000);
        }

        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                const maxSize = parseInt(input.getAttribute('data-max-size'));
                if (input.files[0].size > maxSize) {
                    alert(`File size must be less than ${maxSize/1024/1024}MB`);
                    input.value = '';
                    return false;
                }
            }
            return true;
        }

        function filterBooks(searchTerm) {
            const terms = searchTerm.toLowerCase().split(/[\s,]+/).filter(term => term.length > 0);
            bookCards.forEach(card => {
                const title = card.querySelector('.card-title span:nth-child(2)').textContent.toLowerCase();
                const author = card.querySelector('.card-body p:nth-child(4) span:nth-child(2)').textContent.toLowerCase();
                const department = card.querySelector('.card-body div:nth-child(3) span:nth-child(2)').textContent.toLowerCase();
                
                let matches = terms.some(term => 
                    title.includes(term) || 
                    author.includes(term) || 
                    department.includes(term) ||
                    levenshtein(term, title) < term.length / 2 ||
                    levenshtein(term, author) < term.length / 2 ||
                    levenshtein(term, department) < term.length / 2
                );
                
                card.style.display = matches ? 'block' : 'none';
            });
        }

        function levenshtein(a, b) {
            const matrix = Array(b.length + 1).fill(null).map(() =>
                Array(a.length + 1).fill(null)
            );
            for (let i = 0; i <= a.length; i++) matrix[0][i] = i;
            for (let j = 0; j <= b.length; j++) matrix[j][0] = j;
            for (let j = 1; j <= b.length; j++) {
                for (let i = 1; i <= a.length; i++) {
                    const indicator = a[i - 1] === b[j - 1] ? 0 : 1;
                    matrix[j][i] = Math.min(
                        matrix[j][i - 1] + 1,
                        matrix[j - 1][i] + 1,
                        matrix[j - 1][i - 1] + indicator
                    );
                }
            }
            return matrix[b.length][a.length];
        }

        searchInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            const searchTerm = this.value.trim();
            debounceTimer = setTimeout(() => {
                if (searchTerm.length > 0) {
                    filterBooks(searchTerm);
                } else {
                    bookCards.forEach(card => card.style.display = 'block');
                }
            }, 300);
        });

        refreshBooksBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });

        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                validateFileSize(this);
            });
        });

        var editModal = document.getElementById('editBookModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var description = button.getAttribute('data-description');
            var date = button.getAttribute('data-date');
            var department = button.getAttribute('data-department');
            var author = button.getAttribute('data-author');
            var isread = button.getAttribute('data-isread');
            var isdownload = button.getAttribute('data-isdownload');
            var cover = button.getAttribute('data-cover');
            var file = button.getAttribute('data-file');

            var modal = this;
            modal.querySelector('#edit_id').value = id;
            modal.querySelector('#edit_book_title').value = title;
            modal.querySelector('#edit_book_description').value = description;
            modal.querySelector('#edit_book_date').value = date;
            modal.querySelector('#edit_department').value = department;
            modal.querySelector('#edit_author').value = author;
            modal.querySelector('#edit_readCheckbox').checked = (isread == '1');
            modal.querySelector('#edit_downloadCheckbox').checked = (isdownload == '1');
            modal.querySelector('#existing_cover').value = cover;
            modal.querySelector('#existing_file').value = file;
            modal.querySelector('#current_cover').src = '/bookstore/book/' + cover;
            modal.querySelector('#current_cover').style.display = 'block';
        });

        document.querySelectorAll('.delete-book').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const bookId = this.getAttribute('data-id');
                const coverPath = this.getAttribute('data-cover');
                const filePath = this.getAttribute('data-file');
                
                if (confirm('Are you sure you want to delete this book?')) {
                    const formData = new FormData();
                    formData.append('delete_id', bookId);
                    formData.append('cover_path', coverPath);
                    formData.append('file_path', filePath);
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showMessage('success', data.message);
                            this.closest('.col-lg-4').remove();
                            if (document.querySelectorAll('.col-lg-4').length === 0) {
                                window.location.reload();
                            }
                        } else {
                            showMessage('danger', data.message);
                        }
                    })
                    .catch(error => {
                        showMessage('danger', 'An error occurred while deleting the book.');
                    })
                    .finally(() => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                    });
                }
            });
        });

        document.getElementById('addBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const coverInput = document.getElementById('add_book_cover');
            const fileInput = document.getElementById('add_book_file');
            
            if (!validateFileSize(coverInput)) return;
            if (!validateFileSize(fileInput)) return;
            
            const formData = new FormData(this);
            formData.append('add_book', '1');
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('success', data.message);
                    const addModal = bootstrap.Modal.getInstance(document.getElementById('addBookModal'));
                    addModal.hide();
                    this.reset();
                    window.location.reload();
                } else {
                    showMessage('danger', data.message);
                }
            })
            .catch(error => {
                showMessage('danger', 'An error occurred while adding the book.');
            })
            .finally(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            });
        });

        document.getElementById('editBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const coverInput = document.getElementById('edit_book_cover');
            const fileInput = document.getElementById('edit_book_file');
            
            if (coverInput.files.length > 0 && !validateFileSize(coverInput)) return;
            if (conflicts.length > 0 && !validateFileSize(fileInput)) return;
            
            const formData = new FormData(this);
            formData.append('edit_book', '1');
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('success', data.message);
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editBookModal'));
                    editModal.hide();
                    window.location.reload();
                } else {
                    showMessage('danger', data.message);
                }
            })
            .catch(error => {
                showMessage('danger', 'An error occurred while updating the book.');
            })
            .finally(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            });
        });
    });
    </script>
</body>
</html>