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

$authors = [];
$departments = [];

$author_result = $conn->query("SELECT name FROM authors");
$department_result = $conn->query("SELECT name FROM categories");

while ($row = $author_result->fetch_assoc()) $authors[] = $row['name'];
while ($row = $department_result->fetch_assoc()) $departments[] = $row['name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = '';

    $book_date = $_POST['book_date'] ?? null;
    $book_title = trim($_POST['book_title'] ?? '');
    $book_description = trim($_POST['book_description'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $is_read = isset($_POST['readCheckbox']) ? 1 : 0;
    $is_download = isset($_POST['downloadCheckbox']) ? 1 : 0;
    $book_cover = $_FILES['book_cover'];
    $book_file = $_FILES['book_file'];

    if (empty($book_date)) $message .= "<p class='error'>Please select a date.</p>";
    if (empty($book_title)) $message .= "<p class='error'>Book title is required.</p>";
    if (empty($book_description)) $message .= "<p class='error'>Book description is required.</p>";
    if (empty($department)) $message .= "<p class='error'>Please enter or select a department.</p>";
    if (empty($author)) $message .= "<p class='error'>Please enter or select an author.</p>";

    if ($book_cover['error'] !== UPLOAD_ERR_OK) {
        $message .= "<p class='error'>Error uploading book cover.</p>";
    } else {
        $cover_ext = strtolower(pathinfo($book_cover['name'], PATHINFO_EXTENSION));
        if (!in_array($cover_ext, ['png', 'jpg', 'jpeg'])) {
            $message .= "<p class='error'>Invalid book cover format! Please upload a PNG or JPEG image.</p>";
        }
        if ($book_cover['size'] > 20971520) {
            $message .= "<p class='error'>Book cover must be less than 20MB</p>";
        }
    }

    if ($book_file['error'] !== UPLOAD_ERR_OK) {
        $message .= "<p class='error'>Error uploading book file.</p>";
    } elseif ($book_file['type'] !== 'application/pdf') {
        $message .= "<p class='error'>Invalid file format! Please upload a PDF file.</p>";
    } elseif ($book_file['size'] > 20971520) {
        $message .= "<p class='error'>Book file must be less than 20MB</p>";
    }

    if (empty($message)) {
        $check_stmt = $conn->prepare("SELECT id FROM books WHERE title = ? AND author = ?");
        $check_stmt->bind_param("ss", $book_title, $author);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $message .= "<p class='error'>A book with the same title and author already exists.</p>";
        }
        $check_stmt->close();
    }

    if (empty($message)) {
        $authors_db = array_column($conn->query("SELECT name FROM authors")->fetch_all(MYSQLI_ASSOC), 'name');
        $departments_db = array_column($conn->query("SELECT name FROM categories")->fetch_all(MYSQLI_ASSOC), 'name');

        if (!in_array($author, $authors_db)) {
            $author_stmt = $conn->prepare("INSERT INTO authors (name) VALUES (?)");
            $author_stmt->bind_param("s", $author);
            $author_stmt->execute();
            $author_stmt->close();
            $authors[] = $author;
        }

        if (!in_array($department, $departments_db)) {
            $dept_stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $dept_stmt->bind_param("s", $department);
            $dept_stmt->execute();
            $dept_stmt->close();
            $departments[] = $department;
        }

        $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/';
        $cover_dir = $base_dir . 'Uploads/covers/';
        $file_dir = $base_dir . 'Uploads/files/';
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
                $message = "<p class='success'>New book added successfully!</p>";
            } else {
                $message = "<p class='error'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='error'>Error uploading files! Please check your file permissions.</p>";
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['message' => $message]);
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="stylesheet" href="css/add book.css">
</head>
<body>

    <h1>Add New Book</h1>
    <div class="container">
        <form id="book-form" method="post" enctype="multipart/form-data">
            <label for="book_date">Date:</label>
            <input type="date" id="book_date" name="book_date" required>

            <label for="book_title">Book Title:</label>
            <input type="text" id="book_title" name="book_title" placeholder="Enter book title" required>

            <label for="book_description">Book Description:</label>
            <input type="text" id="book_description" name="book_description" placeholder="Enter book description" required>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" list="department_list" placeholder="Type or select department" required>
            <datalist id="department_list">
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>">
                <?php endforeach; ?>
            </datalist>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" list="author_list" placeholder="Type or select author" required>
            <datalist id="author_list">
                <?php foreach ($authors as $auth): ?>
                    <option value="<?php echo htmlspecialchars($auth); ?>">
                <?php endforeach; ?>
            </datalist>

            <label for="book_cover">Book Cover (PNG/JPG, max 5MB):</label>
            <input type="file" id="book_cover" name="book_cover" accept="image/png, image/jpeg" required data-max-size="20971520">

            <label for="book_file">Upload File (PDF, max 20MB):</label>
            <input type="file" id="book_file" name="book_file" accept=".pdf" required data-max-size="20971520">

            <div class="checkbox-group">
                <label><input type="checkbox" name="readCheckbox" value="1"> Available For Read</label>
                <label><input type="checkbox" name="downloadCheckbox" value="1"> Available For Download</label>
            </div>

            <button type="submit">Add Book</button>
            
            <div class="message-container" id="message-container"></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settingsToggle = document.getElementById('settings-toggle');
            const themeOptions = document.getElementById('theme-options');

            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

            if (settingsToggle && themeOptions) {
                settingsToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
                });

                document.querySelectorAll('.theme-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const theme = this.getAttribute('data-theme');
                        document.body.className = theme;
                        localStorage.setItem('bookstoreTheme', theme);
                        themeOptions.style.display = 'none';
                    });
                });

                document.addEventListener('click', function(e) {
                    if (!settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                        themeOptions.style.display = 'none';
                    }
                });
            }

            document.getElementById('book-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const coverInput = document.getElementById('book_cover');
                const fileInput = document.getElementById('book_file');
                const maxSize = 20971520;

                if (coverInput.files[0] && coverInput.files[0].size > maxSize) {
                    document.getElementById('message-container').innerHTML = "<p class='error'>Book cover must be less than 20MB</p>";
                    return;
                }
                if (fileInput.files[0] && fileInput.files[0].size > maxSize) {
                    document.getElementById('message-container').innerHTML = "<p class='error'>Book file must be less than 20MB</p>";
                    return;
                }

                const formData = new FormData(this);
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('message-container');
                    messageContainer.innerHTML = data.message;
                    button.disabled = false;
                    messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                    if (data.message.includes('success')) {
                        this.reset();
                        setTimeout(() => {
                            messageContainer.innerHTML = '';
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message-container').innerHTML = "<p class='error'>An error occurred. Please try again.</p>";
                    button.disabled = false;
                });
            });
        });
    </script>
</body>
</html>