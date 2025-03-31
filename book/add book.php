<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="css/add book.css">
</head>
<body>
    <h1>Add New Book</h1>
    <div class="container">
        <form action="process_add_book.php" method="post" enctype="multipart/form-data">
            <label for="book_date">Date:</label>
            <input type="date" id="book_date" name="book_date" required>

            <label for="book_title">Book Title:</label>
            <input type="text" id="book_title" name="book_title" placeholder="Enter book title" required>

            <label for="book_description">Book Description:</label>
            <input type="text" id="book_description" name="book_description" placeholder="Enter book description" required>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" placeholder="Search department" required>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" placeholder="Search author" required>

            <label for="book_cover">Book Cover:</label>
            <input type="file" id="book_cover" name="book_cover" accept="image/*" required>

            <label for="book_file">Upload File (PDF):</label>
            <input type="file" id="book_file" name="book_file" accept=".pdf" required>

            <div class="checkbox-group">
                <label><input type="checkbox" id="readCheckbox"> Read</label>
                <label><input type="checkbox" id="downloadCheckbox"> Download</label>
            </div>

            <button type="submit">Add Book</button>
        </form>
    </div>
</body>
</html>