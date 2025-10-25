<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Message variable for feedback
$message = '';

// Create User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $role, $password);
    $stmt->execute();
    $stmt->close();
    header("Location: user.php");
    exit();
}

// Update User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $role, $password, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: user.php");
    exit();
}

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: user.php");
    exit();
}

// Fetch Users
$result = $conn->query("SELECT * FROM users");

// Fetch User to Edit
$userToEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userToEdit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Players</title>
    <link rel="stylesheet" href="assets/user_style.css">
</head>
<body>
    <h2 id="user">Manage Players</h2>

    <?php if ($userToEdit): ?>
        <!-- Update User Form -->
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $userToEdit['id']; ?>">
            <input type="text" name="username" placeholder="Username" value="<?php echo $userToEdit['username']; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $userToEdit['email']; ?>" required>
            <input type="text" name="role" placeholder="Role" value="<?php echo $userToEdit['role']; ?>" required>
            <input type="password" name="password" placeholder="Password" value="<?php echo $userToEdit['password']; ?>" required>
            <button type="submit" name="update">Update User</button>
            <a href="user.php">Cancel</a>
        </form>
    <?php else: ?>
        <!-- Create User Form -->
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="role" placeholder="Role" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="create">Add Player</button>
        </form>
    <?php endif; ?>

    <h3>Existing Users</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p><?php echo "{$row['username']} - {$row['email']} - Role: {$row['role']}"; ?>
        <a href="user.php?edit=<?php echo $row['id']; ?>">Edit</a>
        <a href="user.php?delete=<?php echo $row['id']; ?>">Delete</a></p>
    <?php endwhile; ?>
    <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
</body>
</html>
