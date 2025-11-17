<?php
require_once 'db_connection.php';

// Get all winners
$positions = $conn->query("SELECT * FROM positions WHERE posStat='active'");
$all_winners = array();

while ($pos = $positions->fetch_assoc()) {
    $posID = $pos['posID'];
    $limit = $pos['posNumPositions'];
    $winners = $conn->query("SELECT c.*, COUNT(v.voteID) as votes, p.posName FROM candidates c LEFT JOIN votes v ON c.candID = v.candID LEFT JOIN positions p ON c.posID = p.posID WHERE c.posID=$posID AND c.candStat='active' GROUP BY c.candID ORDER BY votes DESC LIMIT $limit");
    while ($w = $winners->fetch_assoc()) {
        $all_winners[] = $w;
    }
}

usort($all_winners, function($a, $b) {
    return $b['votes'] - $a['votes'];
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Election Winners</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Election Winners</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button w3-green">Winners</a>
    </div>

    <div class="w3-container w3-margin-top">
        <table class="w3-table w3-striped w3-bordered w3-hoverable">
            <tr class="w3-green">
                <th>Elective Position</th>
                <th>Winner</th>
                <th>Total Votes</th>
            </tr>
            <?php foreach ($all_winners as $w): ?>
                <tr>
                    <td><?php echo $w['posName']; ?></td>
                    <td><?php echo $w['candFName'] . ' ' . $w['candLName']; ?></td>
                    <td><?php echo $w['votes']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>