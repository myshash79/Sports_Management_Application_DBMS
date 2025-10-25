<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$message = ""; // For storing success/error messages

// Fetch Teams and Venues
$teams = $conn->query("SELECT * FROM teams");
$venues = $conn->query("SELECT * FROM venues");

// Schedule Match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $date = $_POST['date'];
    $venue_id = $_POST['venue_id'];

    $stmt = $conn->prepare("CALL InsertMatch(?, ?, ?, ?)");
    $stmt->bind_param("iisi", $team1_id, $team2_id, $date, $venue_id);
    if ($stmt->execute()) {
        $message = "Match scheduled successfully using stored procedure!";
    } else {
        $message = "Error scheduling match: " . $conn->error;
    }
    $stmt->close();
}

// Update Match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $match_id = $_POST['match_id'];
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $date = $_POST['date'];
    $venue_id = $_POST['venue_id'];

    $stmt = $conn->prepare("CALL UpdateMatch(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisii", $match_id, $team1_id, $team2_id, $date, $venue_id);
    if ($stmt->execute()) {
        $message = "Match updated successfully using stored procedure!";
    } else {
        $message = "Error updating match: " . $conn->error;
    }
    $stmt->close();
}

// Delete Match
if (isset($_GET['delete'])) {
    $match_id = $_GET['delete'];

    $stmt = $conn->prepare("CALL DeleteMatch(?)");
    $stmt->bind_param("i", $match_id);
    if ($stmt->execute()) {
        $message = "Match deleted successfully using stored procedure!";
    } else {
        $message = "Error deleting match: " . $conn->error;
    }
    $stmt->close();
}

// Fetch Matches
$result = $conn->query("SELECT matches.id, t1.name as team1, t2.name as team2, matches.date, venues.name as venue
                        FROM matches
                        JOIN teams t1 ON matches.team1_id = t1.id
                        JOIN teams t2 ON matches.team2_id = t2.id
                        JOIN venues ON matches.venue_id = venues.id");

// Fetch Match Logs
$log_result = $conn->query("SELECT * FROM match_log ORDER BY log_timestamp DESC");

// Fetch Venue for Latest Match (Nested Query)
$latest_venue_result = $conn->query("
    SELECT venues.name AS venue_name
    FROM venues
    WHERE venues.id = (
        SELECT venue_id 
        FROM matches 
        WHERE date = (SELECT MAX(date) FROM matches)
        LIMIT 1
    );
");
$latest_venue = $latest_venue_result->fetch_assoc()['venue_name'] ?? "No matches scheduled.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Matches</title>
    <link rel="stylesheet" href="assets/match_styles.css">
</head>
<body>
    <h2 class="matches">Manage Matches</h2>

    <!-- Display the venue for the latest match -->
    <p><strong>Venue for the Latest Match:</strong> <?php echo $latest_venue; ?></p>

    <!-- Display success or error message -->
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Form to create a new match -->
    <form method="POST">
        <label>Team 1:</label>
        <select name="team1_id">
            <?php while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <label>Team 2:</label>
        <select name="team2_id">
            <?php $teams->data_seek(0); while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="date" name="date" required>
        <label>Venue:</label>
        <select name="venue_id">
            <?php while ($venue = $venues->fetch_assoc()): ?>
                <option value="<?php echo $venue['id']; ?>"><?php echo $venue['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="create">Schedule Match (via Stored Procedure)</button>
    </form>

    <!-- Display existing matches with edit and delete options -->
    <h3>Existing Matches</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p><?php echo "{$row['team1']} vs {$row['team2']} on {$row['date']} at {$row['venue']}"; ?>
        <a href="match.php?delete=<?php echo $row['id']; ?>">Delete</a>
        <a href="match.php?edit=<?php echo $row['id']; ?>">Edit</a></p>
    <?php endwhile; ?>

    <!-- Form to update a match (only displayed when editing) -->
    <?php if (isset($_GET['edit'])): ?>
        <?php
            $match_id = $_GET['edit'];
            $match_result = $conn->query("SELECT * FROM matches WHERE id=$match_id");
            $match = $match_result->fetch_assoc();
        ?>
        <h3>Edit Match</h3>
        <form method="POST">
            <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
            <label>Team 1:</label>
            <select name="team1_id">
                <?php $teams->data_seek(0); while ($team = $teams->fetch_assoc()): ?>
                    <option value="<?php echo $team['id']; ?>" <?php echo ($team['id'] == $match['team1_id']) ? 'selected' : ''; ?>>
                        <?php echo $team['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>Team 2:</label>
            <select name="team2_id">
                <?php $teams->data_seek(0); while ($team = $teams->fetch_assoc()): ?>
                    <option value="<?php echo $team['id']; ?>" <?php echo ($team['id'] == $match['team2_id']) ? 'selected' : ''; ?>>
                        <?php echo $team['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="date" value="<?php echo $match['date']; ?>" required>
            <label>Venue:</label>
            <select name="venue_id">
                <?php $venues->data_seek(0); while ($venue = $venues->fetch_assoc()): ?>
                    <option value="<?php echo $venue['id']; ?>" <?php echo ($venue['id'] == $match['venue_id']) ? 'selected' : ''; ?>>
                        <?php echo $venue['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="update">Update Match (via Stored Procedure)</button>
        </form>
    <?php endif; ?>

    <!-- Display match logs -->
    <h3>Match Logs</h3>
    <?php while ($log = $log_result->fetch_assoc()): ?>
        <p>
            Match ID: <?php echo $log['match_id']; ?> | 
            Teams: <?php echo "{$log['team1_id']} vs {$log['team2_id']}"; ?> | 
            Date: <?php echo $log['match_date']; ?> | 
            Venue: <?php echo $log['venue_id']; ?> | 
            Logged at: <?php echo $log['log_timestamp']; ?>
        </p>
    <?php endwhile; ?>
    <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
</body>
</html>
