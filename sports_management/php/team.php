<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Create Team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $coach_name = $_POST['coach_name'];

    $stmt = $conn->prepare("INSERT INTO teams (name, coach_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $coach_name);
    $stmt->execute();
    $stmt->close();
    header("Location: team.php");
    exit();
}

// Update Team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $coach_name = $_POST['coach_name'];

    $stmt = $conn->prepare("UPDATE teams SET name = ?, coach_name = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $coach_name, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: team.php");
    exit();
}

// Delete Team
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM teams WHERE id=$id");
    header("Location: team.php");
    exit();
}

// Fetch Teams
$result = $conn->query("SELECT * FROM teams");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Teams</title>
    <link rel="stylesheet" href="assets/team_style.css">
</head>
<body>
    <h2 class="teams">Manage Teams</h2>
    
    <!-- Form to create a new team -->
    <form method="POST">
        <input type="text" name="name" placeholder="Team Name" required>
        <input type="text" name="coach_name" placeholder="Coach Name" required>
        <button type="submit" name="create">Create Team</button>
    </form>

    <!-- Display existing teams -->
    <h3>Existing Teams</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p>
            <?php echo $row['name'] . " - Coach: " . $row['coach_name']; ?>
            <a href="team.php?delete=<?php echo $row['id']; ?>">Delete</a>
            <!-- Button to edit team (shows update form) -->
            <a href="team.php?edit=<?php echo $row['id']; ?>">Edit</a>
        </p>
    <?php endwhile; ?>

    <!-- Form to update a team (only displayed when editing) -->
    <?php if (isset($_GET['edit'])): ?>
        <?php
            $id = $_GET['edit'];
            $team_result = $conn->query("SELECT * FROM teams WHERE id=$id");
            $team = $team_result->fetch_assoc();
        ?>
        <h3>Edit Team</h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $team['id']; ?>">
            <input type="text" name="name" placeholder="Team Name" value="<?php echo $team['name']; ?>" required>
            <input type="text" name="coach_name" placeholder="Coach Name" value="<?php echo $team['coach_name']; ?>" required>
            <button type="submit" name="update">Update Team</button>
        </form>
    <?php endif; ?>
    <button  onclick="window.location.href='dashboard.php'">Back to Dashboard</button>

</body>
</html>
