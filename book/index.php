<?php 
session_start();
include "db_conn.php";
include "php/func-book.php";
$books = get_all_books($conn);
include "php/func-author.php";
$authors = get_all_author($conn);
include "php/func-category.php";
$categories = get_all_categories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="theme-default power-2">
    <div class="container-fluid" style="height: 100%;">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-book nav-icon"></i> Online Book Store
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">
                                <i class="fas fa-home nav-icon"></i>BookStore Page
                            </a>
                        </li>
                        <div class="menu-group">
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-comment nav-icon"></i>Comment
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-list nav-icon"></i>View Book List
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cog nav-icon"></i>Settings
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                                <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-light', event)">Light <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-light', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-light', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-eye-care', event)">Eye-care <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-eye-care', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-eye-care', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-blue', event)">Blue <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-blue', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-blue', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-pink', event)">Pink <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-pink', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-pink', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-red', event)">Red <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-red', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-red', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-gray', event)">Gray <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-gray', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-gray', 1, event)"></i></span></a></li>
                                    <li><a class="dropdown-item" href="#" onclick="changeTheme('theme-sepia', event)">Sepia <span><i class="fas fa-minus power-icon power-minus" onclick="adjustPower('theme-sepia', -1, event)"></i> <i class="fas fa-plus power-icon power-plus" onclick="adjustPower('theme-sepia', 1, event)"></i></span></a></li>
                                    
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link logout-item" href="#">
                                    <i class="fas fa-sign-out-alt nav-icon"></i>Logout
                                </a>
                            </li>
                        </div>
                        <li class="nav-item">
                            <?php if (isset($_SESSION['user_id'])) {?>
                                <a class="nav-link" href="admin.php">
                                    <i class="fas fa-user-shield nav-icon"></i>Admin
                                </a>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <form action="search.php" method="get" style="width: 100%; max-width: 100%;">
            <div class="search-container">
                <label for="search" class="form-label">Search by:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="book_title" name="book_title" placeholder="Title" aria-label="Search Book Title">
                    <button class="btn btn-primary" type="submit" name="search_type" value="book_title">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" id="author_name" name="author_name" placeholder="Author" aria-label="Search Author Name">
                    <button class="btn btn-primary" type="submit" name="search_type" value="author_name">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" id="category" name="category" placeholder="Department" aria-label="Search Category">
                    <button class="btn btn-primary" type="submit" name="search_type" value="category">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
        <div class="d-flex pt-3" style="flex-wrap: wrap; height: calc(100% - 150px);">
            <?php if ($books == 0) { ?>
                <div class="alert alert-warning text-center p-5" role="alert">
                    <img src="img/empty.png" width="100">
                    <br>
                    There is no book in the database
                </div>
            <?php } else { ?>
                <div class="pdf-list">
                    <?php foreach ($books as $book) { ?>
                        <div class="card m-1">
                            <img src="uploads/cover/<?=$book['cover']?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?=$book['title']?></h5>
                                <p class="card-text">
                                    <i><b>By:
                                    <?php foreach ($authors as $author) { 
                                        if ($author['id'] == $book['author_id']) {
                                            echo $author['name'];
                                            break;
                                        }
                                    } ?>
                                    <br></b></i>
                                    <?=$book['description']?>
                                    <br><i><b>Category:
                                    <?php foreach ($categories as $category) { 
                                        if ($category['id'] == $book['category_id']) {
                                            echo $category['name'];
                                            break;
                                        }
                                    } ?>
                                    <br></b></i>
                                </p>
                                <a href="uploads/files/<?=$book['file']?>" class="btn btn-success">Open</a>
                                <a href="uploads/files/<?=$book['file']?>" class="btn btn-primary" download="<?=$book['title']?>">Download</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="category">
                <div class="list-group">
                    <?php if ($categories != 0) { ?>
                        <a href="#" class="list-group-item list-group-item-action active">Category</a>
                        <?php foreach ($categories as $category) { ?>
                            <a href="category.php?id=<?=$category['id']?>" class="list-group-item list-group-item-action"><?=$category['name']?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="list-group mt-5">
                    <?php if ($authors != 0) { ?>
                        <a href="#" class="list-group-item list-group-item-action active">Author</a>
                        <?php foreach ($authors as $author) { ?>
                            <a href="author.php?id=<?=$author['id']?>" class="list-group-item list-group-item-action"><?=$author['name']?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    <script src="css/index.js"></script>
</body>
</html>