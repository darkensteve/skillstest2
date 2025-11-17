<?php
require_once 'db_connection.php';

// Get all positions
$positions = $conn->query("SELECT * FROM positions WHERE posStat='active'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Election Results</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Election Results</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button w3-green">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button">Winners</a>
    </div>

    <?php while ($pos = $positions->fetch_assoc()): 
        $posID = $pos['posID'];
        $total_votes = $conn->query("SELECT COUNT(*) as total FROM votes WHERE posID=$posID")->fetch_assoc()['total'];
        $candidates = $conn->query("SELECT c.*, COUNT(v.voteID) as votes FROM candidates c LEFT JOIN votes v ON c.candID = v.candID WHERE c.posID=$posID AND c.candStat='active' GROUP BY c.candID ORDER BY votes DESC");
    ?>
        <div class="w3-container w3-card-4 w3-margin-bottom w3-padding">
            <h3><?php echo $pos['posName']; ?></h3>
            <table class="w3-table w3-striped w3-bordered w3-hoverable">
                <tr class="w3-green">
                    <th>Candidate</th>
                    <th>Total Votes</th>
                    <th>Voting %</th>
                </tr>
                <?php while ($cand = $candidates->fetch_assoc()): 
                    $percentage = $total_votes > 0 ? ($cand['votes'] / $total_votes) * 100 : 0;
                ?>
                    <tr>
                        <td><?php echo $cand['candFName'] . ' ' . $cand['candLName']; ?></td>
                        <td><?php echo $cand['votes']; ?></td>
                        <td><?php echo number_format($percentage, 2); ?>%</td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php endwhile; ?>
</body>
</html>