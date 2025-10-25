<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = '';

// Create Venue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO venues (name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $location);
    if ($stmt->execute()) {
        $message = "Venue added successfully!";
    } else {
        $message = "Error adding venue: " . $conn->error;
    }
    $stmt->close();
    header("Location: venue.php");
    exit();
}

// Update Venue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("UPDATE venues SET name = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $location, $id);
    if ($stmt->execute()) {
        $message = "Venue updated successfully!";
    } else {
        $message = "Error updating venue: " . $conn->error;
    }
    $stmt->close();
    header("Location: venue.php");
    exit();
}

// Delete Venue
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($conn->query("DELETE FROM venues WHERE id=$id")) {
        $message = "Venue deleted successfully!";
    } else {
        $message = "Error deleting venue: " . $conn->error;
    }
    header("Location: venue.php");
    exit();
}

// Fetch Venues
$result = $conn->query("SELECT * FROM venues");

// Fetch Venue to Update
$venueToEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM venues WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $venueToEdit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch Venue for Latest Match (Nested Query)
$latestVenueResult = $conn->query("
    SELECT venues.name AS venue_name
    FROM venues
    WHERE venues.id = (
        SELECT venue_id 
        FROM matches 
        WHERE date = (SELECT MAX(date) FROM matches)
        LIMIT 1
    );
");
$latestVenue = $latestVenueResult->fetch_assoc()['venue_name'] ?? "No matches scheduled.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Venues</title>
    <link rel="stylesheet" href="assets/team_style.css">
</head>
<body>
    <h2 id="venues">Manage Venues</h2>

    <!-- Display Messages -->
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Display Venue for Latest Match -->
    <p><strong>Venue for the Latest Match:</strong> <?php echo $latestVenue; ?></p>

    <!-- Venue Forms -->
    <?php if ($venueToEdit): ?>
        <!-- Update Venue Form -->
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $venueToEdit['id']; ?>">
            <input type="text" name="name" placeholder="Venue Name" value="<?php echo $venueToEdit['name']; ?>" required>
            <input type="text" name="location" placeholder="Location" value="<?php echo $venueToEdit['location']; ?>" required>
            <button type="submit" name="update">Update Venue</button>
            <a href="venue.php">Cancel</a>
        </form>
    <?php else: ?>
        <!-- Create Venue Form -->
        <form method="POST">
            <input type="text" name="name" placeholder="Venue Name" required>
            <input type="text" name="location" placeholder="Location" required>
            <button type="submit" name="create">Add Venue</button>
        </form>
    <?php endif; ?>

    <!-- Existing Venues -->
    <h3>Existing Venues</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p><?php echo $row['name'] . " - Location: " . $row['location']; ?>
        <a href="venue.php?edit=<?php echo $row['id']; ?>">Edit</a>
        <a href="venue.php?delete=<?php echo $row['id']; ?>">Delete</a></p>
    <?php endwhile; ?>
    <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
</body>
</html>
