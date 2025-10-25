<?php
$servername = "127.0.0.1";  
$username = "root"; 
$password = ""; 
$dbname = "new_sports";
$port = 3307; 
// Connect to the database with the custom port
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css">

    <title>Document</title>
</head>
<body>
    
</body>
</html>