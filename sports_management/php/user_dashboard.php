<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch Tournaments for User View
$tournaments = $conn->query("SELECT * FROM tournaments");

// Fetch Matches with Results
$results = $conn->query("
    SELECT matches.date, t1.name AS team1, t2.name AS team2, venues.name AS venue,
           results.team1_score, results.team2_score
    FROM matches
    JOIN teams t1 ON matches.team1_id = t1.id
    JOIN teams t2 ON matches.team2_id = t2.id
    JOIN venues ON matches.venue_id = venues.id
    LEFT JOIN results ON matches.id = results.match_id
    ORDER BY matches.date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/user_dashboard_style.css">
</head>
<body>
    <h2 id="ud">Welcome to the User Dashboard</h2>
    
    <!-- Display Tournaments -->
    <h3>Existing Tournaments</h3>
    <?php while ($row = $tournaments->fetch_assoc()): ?>
        <p><?php echo $row['name'] . " - " . $row['start_date'] . " to " . $row['end_date']; ?></p>
    <?php endwhile; ?>

    <!-- Display Match Results -->
    <h3>Match Results</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Team 1</th>
                <th>Score</th>
                <th>Team 2</th>
                <th>Score</th>
                <th>Venue</th>
                <th>Winner</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
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
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <li><a href="logout.php">Logout</a></li>
</body>
</html>
