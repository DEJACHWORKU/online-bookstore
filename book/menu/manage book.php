<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new book
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

        // Validate inputs
        $errors = [];
        if (empty($book_date)) $errors[] = "Please select a date.";
        if (empty($book_title)) $errors[] = "Book title is required.";
        if (empty($book_description)) $errors[] = "Book description is required.";
        if (empty($department)) $errors[] = "Department is required.";
        if (empty($author)) $errors[] = "Author is required.";

        // Validate cover image
        if ($book_cover['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading book cover.";
        } else {
            $cover_ext = strtolower(pathinfo($book_cover['name'], PATHINFO_EXTENSION));
            if (!in_array($cover_ext, ['png', 'jpg', 'jpeg'])) {
                $errors[] = "Invalid book cover format! Please upload a PNG or JPEG image.";
            }
        }

        // Validate PDF file
        if ($book_file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading book file.";
        } elseif ($book_file['type'] !== 'application/pdf') {
            $errors[] = "Invalid file format! Please upload a PDF file.";
        }

        // Check for duplicate book
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

        // Process if no errors
        if (empty($errors)) {
            // Create upload directories if they don't exist
            $cover_dir = 'uploads/covers/';
            $file_dir = 'uploads/files/';
            if (!is_dir($cover_dir)) mkdir($cover_dir, 0777, true);
            if (!is_dir($file_dir)) mkdir($file_dir, 0777, true);

            // Generate unique filenames
            $cover_filename = uniqid() . '.' . $cover_ext;
            $file_filename = uniqid() . '.pdf';
            
            $cover_path = $cover_dir . $cover_filename;
            $file_path = $file_dir . $file_filename;

            // Move uploaded files
            if (move_uploaded_file($book_cover['tmp_name'], $cover_path) && 
                move_uploaded_file($book_file['tmp_name'], $file_path)) {
                
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO books (date, title, description, department, author, cover, file, is_read, is_download) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssii", $book_date, $book_title, $book_description, $department, $author, $cover_path, $file_path, $is_read, $is_download);
                
                if ($stmt->execute()) {
                    $message = urlencode("<div class='alert alert-success'>New book added successfully!</div>");
                } else {
                    $message = urlencode("<div class='alert alert-danger'>Error: " . $stmt->error . "</div>");
                }
                $stmt->close();
            } else {
                $message = urlencode("<div class='alert alert-danger'>Error uploading files!</div>");
            }
            header("Location: manage_books.php?message=$message");
            exit;
        } else {
            $message = urlencode("<div class='alert alert-danger'>" . implode("<br>", $errors) . "</div>");
            header("Location: manage_books.php?message=$message");
            exit;
        }
    }
    // Edit existing book
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
        
        // Handle file uploads if new files are provided
        $cover_path = $existing_cover;
        $file_path = $existing_file;
        
        if (!empty($_FILES['book_cover']['name'])) {
            $cover_dir = 'uploads/covers/';
            $cover_ext = strtolower(pathinfo($_FILES['book_cover']['name'], PATHINFO_EXTENSION));
            $cover_filename = uniqid() . '.' . $cover_ext;
            $cover_path = $cover_dir . $cover_filename;
            
            if (move_uploaded_file($_FILES['book_cover']['tmp_name'], $cover_path)) {
                // Delete old cover if it exists
                if (file_exists($existing_cover)) {
                    unlink($existing_cover);
                }
            } else {
                $cover_path = $existing_cover; // Revert to existing if upload fails
            }
        }
        
        if (!empty($_FILES['book_file']['name'])) {
            $file_dir = 'uploads/files/';
            $file_filename = uniqid() . '.pdf';
            $file_path = $file_dir . $file_filename;
            
            if (move_uploaded_file($_FILES['book_file']['tmp_name'], $file_path)) {
                // Delete old file if it exists
                if (file_exists($existing_file)) {
                    unlink($existing_file);
                }
            } else {
                $file_path = $existing_file; // Revert to existing if upload fails
            }
        }
        
        // Update database record
        $stmt = $conn->prepare("UPDATE books SET date=?, title=?, description=?, department=?, author=?, cover=?, file=?, is_read=?, is_download=? WHERE id=?");
        $stmt->bind_param("sssssssiii", $book_date, $book_title, $book_description, $department, $author, $cover_path, $file_path, $is_read, $is_download, $edit_id);
        
        if ($stmt->execute()) {
            $message = urlencode("<div class='alert alert-success'>Book updated successfully!</div>");
        } else {
            $message = urlencode("<div class='alert alert-danger'>Error updating book: " . $stmt->error . "</div>");
        }
        $stmt->close();
        header("Location: manage_books.php?message=$message");
        exit;
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // First get file paths to delete the physical files
    $stmt = $conn->prepare("SELECT cover, file FROM books WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($cover_path, $file_path);
    $stmt->fetch();
    $stmt->close();
    
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        // Delete the physical files
        if (file_exists($cover_path)) unlink($cover_path);
        if (file_exists($file_path)) unlink($file_path);
        
        $message = urlencode("<div class='alert alert-success'>Book deleted successfully!</div>");
    } else {
        $message = urlencode("<div class='alert alert-danger'>Error deleting book: " . $stmt->error . "</div>");
    }
    $stmt->close();
    header("Location: manage_books.php?message=$message");
    exit;
}

// Handle search
$search_query = "";
$books = [];
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_term = "%$search_query%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR department LIKE ? ORDER BY date DESC");
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: var(--dark-color);
        }
        
        .top-bar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .search-input {
            border: none;
            outline: none;
            padding: 0.5rem 1rem;
            width: 100%;
        }
        
        .search-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .search-btn:hover {
            background: #2980b9;
        }
        
        .action-btn {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .card-body {
            padding: 1.5rem;
            position: relative;
            padding-bottom: 3rem;
        }
        
        .card-title {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.75rem;
        }
        
        .card-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .data-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-right: 0.5rem;
        }
        
        .data-value {
            color: #555;
        }
        
        .author-text {
            font-style: italic;
            color: #7f8c8d;
        }
        
        .action-buttons {
            position: absolute;
            bottom: 1rem;
            left: 1.5rem;
            right: 1.5rem;
        }
        
        .action-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .btn-view {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-edit {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-delete {
            background-color: var(--accent-color);
            color: white;
        }
        
        .availability-indicators {
            display: flex;
            gap: 10px;
            margin-bottom: 1rem;
        }
        
        .availability-indicator {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: #555;
        }
        
        .indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .read-indicator {
            background-color: #2ecc71;
        }
        
        .download-indicator {
            background-color: #3498db;
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1.5rem;
            }
            
            .action-buttons .btn {
                width: 100%;
                margin-right: 0;
            }
            
            .top-bar {
                padding: 0.5rem 0;
            }
        }
        
        /* Animation for alerts */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <!-- Top Bar with Search and Add Button -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <form method="GET" action="" class="d-flex search-box">
                        <input type="text" class="search-input" name="search" placeholder="Search books by title, author or department..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-light action-btn me-2" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fas fa-plus me-2"></i> Add Book
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Books Listing -->
        <div class="row">
            <?php if (empty($books)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                        <h4>No books found</h4>
                        <p class="mb-0"><?php echo $search_query ? "No results for '$search_query'" : "The book collection is empty"; ?></p>
                        <?php if ($search_query): ?>
                            <a href="manage_books.php" class="btn btn-primary mt-3">Clear Search</a>
                        <?php else: ?>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBookModal">
                                <i class="fas fa-plus me-2"></i> Add Your First Book
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <!-- Book Cover -->
                            <img src="<?php echo htmlspecialchars($book['cover']); ?>" class="card-img-top" alt="Book Cover">
                            
                            <div class="card-body">
                                <!-- Title -->
                                <h5 class="card-title">
                                    <span class="data-label">Title:</span>
                                    <span class="data-value"><?php echo htmlspecialchars($book['title']); ?></span>
                                </h5>
                                
                                <!-- Description -->
                                <p class="card-text">
                                    <span class="data-label">Description:</span>
                                    <span class="data-value"><?php echo htmlspecialchars(substr($book['description'], 0, 100) . (strlen($book['description']) > 100 ? '...' : '')); ?></span>
                                </p>
                                
                                <!-- Readable/Downloadable Indicators -->
                                <div class="availability-indicators">
                                    <?php if ($book['is_read']): ?>
                                        <span class="availability-indicator">
                                            <span class="indicator-dot read-indicator"></span>
                                            Readable
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($book['is_download']): ?>
                                        <span class="availability-indicator">
                                            <span class="indicator-dot download-indicator"></span>
                                            Downloadable
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Department -->
                                <div class="mb-2">
                                    <span class="data-label">Department:</span>
                                    <span class="data-value"><?php echo htmlspecialchars($book['department']); ?></span>
                                </div>
                                
                                <!-- Author and Date -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="author-text mb-0">
                                        <span class="data-label">Author:</span>
                                        <span class="data-value"><?php echo htmlspecialchars($book['author']); ?></span>
                                    </p>
                                    <small class="text-muted">
                                        <span class="data-label">Date:</span>
                                        <span class="data-value"><?php echo date('M d, Y', strtotime($book['date'])); ?></span>
                                    </small>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="action-buttons d-flex flex-wrap">
                                    <a href="<?php echo htmlspecialchars($book['file']); ?>" target="_blank" class="btn btn-sm btn-view">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editBookModal" 
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
                                    <a href="?delete_id=<?php echo $book['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this book?');">
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

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label for="add_book_cover" class="form-label">Book Cover (PNG/JPG)</label>
                                <input class="form-control" type="file" id="add_book_cover" name="book_cover" accept="image/png, image/jpeg" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="add_book_file" class="form-label">Upload File (PDF)</label>
                                <input class="form-control" type="file" id="add_book_file" name="book_file" accept=".pdf" required>
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

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label for="edit_book_cover" class="form-label">Book Cover (PNG/JPG)</label>
                                <input class="form-control" type="file" id="edit_book_cover" name="book_cover" accept="image/png, image/jpeg">
                                <small class="text-muted">Leave blank to keep existing cover</small>
                                <div class="mt-2">
                                    <img id="current_cover" src="" alt="Current Cover" style="max-height: 100px; display: none;" class="img-thumbnail">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="edit_book_file" class="form-label">Upload File (PDF)</label>
                                <input class="form-control" type="file" id="edit_book_file" name="book_file" accept=".pdf">
                                <small class="text-muted">Leave blank to keep existing file</small>
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

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize edit modal with book data
        document.addEventListener('DOMContentLoaded', function() {
            var editBookModal = document.getElementById('editBookModal');
            if (editBookModal) {
                editBookModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    
                    document.getElementById('edit_id').value = button.getAttribute('data-id');
                    document.getElementById('edit_book_title').value = button.getAttribute('data-title');
                    document.getElementById('edit_book_description').value = button.getAttribute('data-description');
                    document.getElementById('edit_book_date').value = button.getAttribute('data-date');
                    document.getElementById('edit_department').value = button.getAttribute('data-department');
                    document.getElementById('edit_author').value = button.getAttribute('data-author');
                    document.getElementById('existing_cover').value = button.getAttribute('data-cover');
                    document.getElementById('existing_file').value = button.getAttribute('data-file');
                    
                    // Set checkboxes
                    document.getElementById('edit_readCheckbox').checked = button.getAttribute('data-isread') === '1';
                    document.getElementById('edit_downloadCheckbox').checked = button.getAttribute('data-isdownload') === '1';
                    
                    // Show current cover
                    var currentCover = document.getElementById('current_cover');
                    currentCover.src = button.getAttribute('data-cover');
                    currentCover.style.display = 'block';
                });
            }
            
            // Auto-scroll to message if present
            if (window.location.hash === '#message') {
                const messageElement = document.querySelector('.alert');
                if (messageElement) {
                    messageElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
</body>
</html>