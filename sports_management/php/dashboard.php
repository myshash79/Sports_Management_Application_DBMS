<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Aggregate Statistics Queries
// Total number of matches
$total_matches_result = $conn->query("SELECT COUNT(*) AS total_matches FROM matches");
$total_matches = $total_matches_result->fetch_assoc()['total_matches'];

// Average number of players per team
$avg_players_result = $conn->query("SELECT AVG(player_count) AS avg_players FROM 
                                   (SELECT COUNT(*) AS player_count FROM players GROUP BY team_id) AS team_players");
$avg_players = $avg_players_result->fetch_assoc()['avg_players'];

// Total matches played by each team
$matches_per_team_result = $conn->query("SELECT teams.name, COUNT(*) AS matches_played 
                                         FROM matches 
                                         JOIN teams ON matches.team1_id = teams.id OR matches.team2_id = teams.id 
                                         GROUP BY teams.name");

// Total players per team
$players_per_team_result = $conn->query("SELECT teams.name, COUNT(players.id) AS total_players 
                                         FROM players 
                                         JOIN teams ON players.team_id = teams.id 
                                         GROUP BY teams.name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/dashboard_style.css">
</head>
<body>
    <div id="dashboard">
        <h2>Welcome, Admin!</h2>
        
        <div class="statistics">
            <h3>Aggregate Statistics</h3>
            
            <p><strong>Total Matches:</strong> <?php echo $total_matches; ?></p>
            <!-- <p><strong>Average Players Per Team:</strong> <?php echo number_format($avg_players, 2); ?></p> -->

            <h4>Matches Played by Each Team:</h4>
            <ul>
                <?php while ($team_match = $matches_per_team_result->fetch_assoc()): ?>
                    <li><?php echo "{$team_match['name']}: {$team_match['matches_played']} matches"; ?></li>
                <?php endwhile; ?>
            </ul>

            <!-- <h4>Players per Team:</h4> -->
            <ul>
                <?php while ($team_players = $players_per_team_result->fetch_assoc()): ?>
                    <li><?php echo "{$team_players['name']}: {$team_players['total_players']} players"; ?></li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div id="dashboard_items">
            <ul>
                <li><a href="tournament.php">Manage Tournaments</a></li>
                <li><a href="team.php">Manage Teams</a></li>
                <li><a href="match.php">Manage Matches</a></li>
                <li><a href="venue.php">Manage Venues</a></li>
                <li><a href="user.php">Manage Users</a></li>
                <li><a href="results.php">Manage Results</a></li>
                <li><a href="gallery.php">Manage Gallery</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
