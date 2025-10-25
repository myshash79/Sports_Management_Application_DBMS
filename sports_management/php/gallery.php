<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Upload Image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $description = $_POST['description'];
    $image_path = 'uploads/' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);

    $stmt = $conn->prepare("INSERT INTO gallery (image_path, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $image_path, $description);
    $stmt->execute();
    $stmt->close();
    header("Location: gallery.php");
}

// Fetch Gallery
$result = $conn->query("SELECT * FROM gallery");
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="assets/styles.css">

<head>
    <title>Manage Gallery</title>
</head>
<h2 id="gallery">Manage Gallery</h2>
<button  onclick="window.location.href='dashboard.php'">Back to Dashboard</button>

<body>
    
