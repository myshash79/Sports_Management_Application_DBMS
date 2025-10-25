<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Create new tournament
        if (empty($_POST['name']) || empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $message = 'All fields are required!';
        } else {
            $name = $_POST['name'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $stmt = $conn->prepare("INSERT INTO tournaments (name, start_date, end_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $start_date, $end_date);
            $stmt->execute();
            $stmt->close();

            $message = 'Tournament added successfully';
            header("Location: tournament.php");
            exit();
        }
    } elseif (isset($_POST['update'])) {
        // Update tournament
        $id = $_POST['id'];
        $name = $_POST['name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $stmt = $conn->prepare("UPDATE tournaments SET name=?, start_date=?, end_date=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $start_date, $end_date, $id);
        if ($stmt->execute()) {
            $message = 'Tournament updated successfully';
        } else {
            $message = 'Error updating tournament';
        }
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Delete tournament
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM tournaments WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $message = 'Tournament deleted successfully';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Tournaments</title>
    <link rel="stylesheet" href="assets/tournament_style.css">
</head>
<body>
    <!-- Back to Dashboard link at the top -->
    <a href="dashboard.php" class="back-to-dashboard">Back to Dashboard</a>

    <h2 class="tournaments">Manage Tournaments</h2>
    
    <!-- Display messages -->
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Tournament Name" required>
        <input type="date" name="start_date" required>
        <input type="date" name="end_date" required>
        <button type="submit" name="create">Create Tournament</button>
    </form>

    <!-- Display tournament list -->
    <h3>Existing Tournaments</h3>
    <?php
    $result = $conn->query("SELECT * FROM tournaments");
    while ($row = $result->fetch_assoc()) {
        ?>
        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
            <input type="date" name="start_date" value="<?php echo $row['start_date']; ?>" required>
            <input type="date" name="end_date" value="<?php echo $row['end_date']; ?>" required>
            <button type="submit" name="update">Update</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this tournament?')">Delete</button>
        </form>
        <?php
    }
    ?>
</body>
</html>
