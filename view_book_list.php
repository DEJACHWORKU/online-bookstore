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
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$department_filter = isset($_GET['department']) ? $_GET['department'] : '';

$sql = "SELECT date, title, description, department, author, cover FROM books WHERE 1=1";
$params = [];
$types = '';

if (!empty($department_filter)) {
    $sql .= " AND department = ?";
    $params[] = $department_filter;
    $types .= 's';
}

if (!empty($sort)) {
    $sql .= ($sort === 'newest') ? " ORDER BY date DESC" : " ORDER BY date ASC";
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/view book list.css">
</head>
<body>
    <div class="menu-bar">
        <div>
            <h1>Welcome to Viewing Book List</h1>
            <div class="subtitle">Filter by department or sort books as you like!</div>
        </div>
        <div>
            <a href="user.php" class="menu-btn bookstore"><i class="fas fa-book"></i> Go to Bookstore</a>
            <button onclick="window.location.href='index.php'" class="menu-btn logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
    </div>
    
    <div class="container">
        <form id="filterForm" method="GET" action="">
            <div class="controls">
                <div class="control-group">
                    <span class="control-label">Search by Department:</span>
                    <div class="control-input">
                        <input type="text" name="department" list="department_list" placeholder="Type or select" value="<?php echo htmlspecialchars($department_filter); ?>">
                        <datalist id="department_list">
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">Sort by:</span>
                    <div class="sort-selector">
                        <div class="sort-trigger" id="sortTrigger">
                            <?php echo empty($sort) ? 'Select sort option' : ($sort === 'newest' ? 'Newest First' : 'Oldest First'); ?>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="sort-options" id="sortOptions">
                            <div class="sort-option <?php echo $sort === 'newest' ? 'active' : ''; ?>" data-value="newest">Newest First</div>
                            <div class="sort-option <?php echo $sort === 'oldest' ? 'active' : ''; ?>" data-value="oldest">Oldest First</div>
                        </div>
                        <input type="hidden" name="sort" id="sortInput" value="<?php echo $sort; ?>">
                    </div>
                </div>
            </div>
        </form>

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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const departmentInput = document.querySelector('input[name="department"]');
            const sortTrigger = document.getElementById('sortTrigger');
            const sortOptions = document.getElementById('sortOptions');
            const sortInput = document.getElementById('sortInput');
            const sortOptionElements = document.querySelectorAll('.sort-option');

            sortTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                sortOptions.classList.toggle('show');
            });

            sortOptionElements.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    
                    sortTrigger.innerHTML = text + ' <i class="fas fa-chevron-down"></i>';
                    sortTrigger.style.color = '#333';
                    sortInput.value = value;
                    
                    sortOptionElements.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    
                    sortOptions.classList.remove('show');
                    
                    form.submit();
                });
            });

            document.addEventListener('click', function() {
                sortOptions.classList.remove('show');
            });

            departmentInput.addEventListener('change', function() {
                form.submit();
            });
        });
    </script>
</body>
</html>