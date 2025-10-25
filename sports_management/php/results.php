<?php
session_start();
include 'db.php';

// Check if logged in
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Message variable for feedback
$message = '';

// Admin - Add or Update Result
if (isset($_SESSION['admin']) && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_result']) || isset($_POST['update_result']))) {
    $match_id = $_POST['match_id'];
    $team1_score = $_POST['team1_score'];
    $team2_score = $_POST['team2_score'];

    // Insert or update result
    $stmt = $conn->prepare("INSERT INTO results (match_id, team1_score, team2_score) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE team1_score = VALUES(team1_score), team2_score = VALUES(team2_score)");
    $stmt->bind_param("iii", $match_id, $team1_score, $team2_score);
    $stmt->execute();
    $stmt->close();
    $message = isset($_POST['update_result']) ? "Result updated successfully" : "Result added successfully";
}

// Delete result if admin
if (isset($_SESSION['admin']) && isset($_GET['delete'])) {
    $match_id = $_GET['delete'];
    $conn->query("DELETE FROM results WHERE match_id=$match_id");
    $message = "Result deleted successfully";
}

// Fetch Matches with Results
$result = $conn->query("
    SELECT matches.id AS match_id, t1.name AS team1, t2.name AS team2, matches.date, venues.name AS venue,
           results.team1_score, results.team2_score
    FROM matches
    JOIN teams t1 ON matches.team1_id = t1.id
    JOIN teams t2 ON matches.team2_id = t2.id
    JOIN venues ON matches.venue_id = venues.id
    LEFT JOIN results ON matches.id = results.match_id
    ORDER BY matches.date DESC
");

// Fetch match to update, if any
$matchToEdit = null;
if (isset($_GET['edit']) && isset($_SESSION['admin'])) {
    $match_id = $_GET['edit'];
    $stmt = $conn->prepare("
        SELECT matches.id AS match_id, t1.name AS team1, t2.name AS team2, matches.date, results.team1_score, results.team2_score
        FROM matches
        JOIN teams t1 ON matches.team1_id = t1.id
        JOIN teams t2 ON matches.team2_id = t2.id
        LEFT JOIN results ON matches.id = results.match_id
        WHERE matches.id = ?
    ");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $matchToEdit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Match Results</title>
    <link rel="stylesheet" href="assets/result_style.css">
</head>
<body>
    <h2 id="ud">Match Results</h2>

    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <!-- Results Table for Users and Admins -->
    <table>
        <thead>
            <tr>
                <div id="results">
                <th>Date</th>
                <th>Team 1</th>
                <th>Score</th>
                <th>Team 2</th>
                <th>Score</th>
                <th>Venue</th>
                <th>Winner</th>
                </div>
                <?php if (isset($_SESSION['admin'])): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['team1']; ?></td>
                    <td><?php echo $row['team1_score'] ?? 'N/A'; ?></td>
                    <td><?php echo $row['team2']; ?></td>
                    <td><?php echo $row['team2_score'] ?? 'N/A'; ?></td>
                    <td><?php echo $row['venue']; ?></td>
                    <td>
                        <?php 
                            if (isset($row['team1_score'], $row['team2_score'])) {
                                if ($row['team1_score'] > $row['team2_score']) {
                                    echo $row['team1'] . " Wins";
                                } elseif ($row['team2_score'] > $row['team1_score']) {
                                    echo $row['team2'] . " Wins";
                                } else {
                                    echo "Draw";
                                }
                            } else {
                                echo "Result Pending";
                            }
                        ?>
                    </td>
                    <?php if (isset($_SESSION['admin'])): ?>
                        <td>
                            <a href="results.php?edit=<?php echo $row['match_id']; ?>">Edit</a>
                            <a href="results.php?delete=<?php echo $row['match_id']; ?>">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Admin-Only: Add or Update Result Form -->
    <?php if (isset($_SESSION['admin'])): ?>
        <?php if ($matchToEdit): ?>
            <!-- Update Match Result Form -->
            <form method="POST" action="results.php">
                <input type="hidden" name="match_id" value="<?php echo $matchToEdit['match_id']; ?>">
                <h3>Update Result for <?php echo "{$matchToEdit['team1']} vs {$matchToEdit['team2']} on {$matchToEdit['date']}"; ?></h3>
                <label for="team1_score">Team 1 Score:</label>
                <input type="number" name="team1_score" value="<?php echo $matchToEdit['team1_score']; ?>" required><br>
                <label for="team2_score">Team 2 Score:</label>
                <input type="number" name="team2_score" value="<?php echo $matchToEdit['team2_score']; ?>" required>
                <button type="submit" name="update_result">Update Result</button>
                <a href="results.php">Cancel</a>
            </form>
        <?php else: ?>
            <!-- Add Match Result Form -->
            <form method="POST" action="results.php">
                <label for="match_id">Match:</label>
                <select name="match_id" required>
                    <?php
                    // Get matches with no existing results
                    $unrecorded_matches = $conn->query("
                        SELECT matches.id, t1.name AS team1, t2.name AS team2, matches.date
                        FROM matches
                        JOIN teams t1 ON matches.team1_id = t1.id
                        JOIN teams t2 ON matches.team2_id = t2.id
                        LEFT JOIN results ON matches.id = results.match_id
                        WHERE results.match_id IS NULL
                    ");
                    while ($match = $unrecorded_matches->fetch_assoc()):
                    ?>
                        <option value="<?php echo $match['id']; ?>">
                            <?php echo "{$match['team1']} vs {$match['team2']} on {$match['date']}"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <label for="team1_score">Team 1 Score:</label>
                <input type="number" name="team1_score" required><br>
                <label for="team2_score">Team 2 Score:</label>
                <input type="number" name="team2_score" required>
                <button type="submit" name="add_result">Add Result</button>
            </form>
           <!-- Back to Dashboard Button -->
<button  onclick="window.location.href='dashboard.php'">Back to Dashboard</button>

        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
