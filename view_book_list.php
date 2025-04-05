<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch departments for filter dropdown
$departments = [];
$dept_result = $conn->query("SELECT DISTINCT name FROM categories");
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row['name'];
}

// Sorting and filtering logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$department_filter = isset($_GET['department']) ? $_GET['department'] : '';

$sql = "SELECT date, title, description, department, author, cover FROM books WHERE 1=1";
$params = [];
$types = '';

if (!empty($department_filter)) {
    $sql .= " AND department = ?";
    $params[] = $department_filter;
    $types .= 's';
}

$sql .= ($sort === 'newest') ? " ORDER BY date DESC" : " ORDER BY date ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Book List</title>
    <link rel="stylesheet" href="css/view book list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="menu-bar">
        <a href="user.php" class="menu-btn bookstore"><i class="fas fa-book"></i> Go to Bookstore</a>
        <button onclick="window.location.href='index.php'" class="menu-btn logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
    <h1>Welcome to Viewing Book List</h1>
    <div class="subtitle">Filter by department or sort books as you like!</div>
    <div class="container">
        <div class="controls">
            <div class="control-group">
                <span class="control-label">Search by Department:</span>
                <div class="control-input">
                    <input type="text" name="department" list="department_list" placeholder="Type or select" value="<?php echo htmlspecialchars($department_filter); ?>" onchange="updateFilter()">
                    <datalist id="department_list">
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">Sort by:</span>
                <div class="control-input">
                    <select name="sort" onchange="updateFilter()">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="book-grid">
            <?php if (empty($books)): ?>
                <p class="no-results">No books found matching your criteria.</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="/bookstore/book/<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover">
                        <div class="book-info">
                            <div><strong>Title:</strong> <span><?php echo htmlspecialchars($book['title']); ?></span></div>
                            <div><strong>Date:</strong> <span><?php echo htmlspecialchars($book['date']); ?></span></div>
                            <div><strong>Department:</strong> <span class="department"><?php echo htmlspecialchars($book['department']); ?></span></div>
                            <div><strong>Description:</strong> <span><?php echo htmlspecialchars($book['description']); ?></span></div>
                            <div><strong>Author:</strong> <span><?php echo htmlspecialchars($book['author']); ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateFilter() {
            const department = document.querySelector('input[name="department"]').value;
            const sort = document.querySelector('select[name="sort"]').value;

            const url = new URL(window.location.href);
            url.searchParams.set('department', department);
            url.searchParams.set('sort', sort);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>