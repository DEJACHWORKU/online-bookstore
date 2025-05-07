<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_GET['file']) || !isset($_GET['title'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied.");
}

$file = $_GET['file'];
$title = $_GET['title'];
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $file;

if (!file_exists($file_path) || !is_readable($file_path)) {
    header("HTTP/1.1 404 Not Found");
    exit("File not found or not accessible.");
}

$extension = pathinfo($file_path, PATHINFO_EXTENSION);
$download_filename = $title . '.' . $extension;

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $download_filename . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
?>