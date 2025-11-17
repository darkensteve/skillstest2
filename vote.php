<?php
session_start();
require_once 'db_connection.php';

$error = "";
$success = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $voterID = $_POST['voterID'];
    $voterPass = $_POST['voterPass'];
    $result = $conn->query("SELECT * FROM voters WHERE voterID=$voterID AND voterPass='$voterPass'");
    
    if ($result->num_rows > 0) {
        $voter = $result->fetch_assoc();
        if ($voter['voterStat'] != 'active') {
            $error = "Your account is not active.";
        } elseif ($voter['voted'] == 'y') {
            $error = "You have already voted.";
        } else {
            $_SESSION['voter_id'] = $voter['voterID'];
            $_SESSION['voter_name'] = $voter['voterFName'] . ' ' . $voter['voterLName'];
        }
    } else {
        $error = "Invalid Voter ID or Password.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: vote.php");
    exit();
}

// Handle voting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_vote']) && isset($_SESSION['voter_id'])) {
    $voterID = $_SESSION['voter_id'];
    $positions = $conn->query("SELECT * FROM positions WHERE posStat='active'");
    $valid = true;
    
    while ($pos = $positions->fetch_assoc()) {
        $posID = $pos['posID'];
        if (isset($_POST['candidate_' . $posID])) {
            $selected = $_POST['candidate_' . $posID];
            if (!is_array($selected)) $selected = array($selected);
            if (count($selected) > $pos['posNumPositions']) {
                $valid = false;
                $error = "Too many candidates selected for " . $pos['posName'];
                break;
            }
        }
    }
    
    if ($valid) {
        $positions->data_seek(0);
        while ($pos = $positions->fetch_assoc()) {
            $posID = $pos['posID'];
            if (isset($_POST['candidate_' . $posID])) {
                $selected = $_POST['candidate_' . $posID];
                if (!is_array($selected)) $selected = array($selected);
                foreach ($selected as $candID) {
                    $conn->query("INSERT INTO votes (voterID, candID, posID) VALUES ($voterID, $candID, $posID)");
                }
            }
        }
        $conn->query("UPDATE voters SET voted='y' WHERE voterID=$voterID");
        $success = "Your vote has been recorded!";
        session_destroy();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vote</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Voting</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button w3-green">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button">Winners</a>
    </div>

    <?php if ($error): ?>
        <div class="w3-panel w3-red"><p><?php echo $error; ?></p></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="w3-panel w3-green"><p><?php echo $success; ?></p></div>
    <?php elseif (!isset($_SESSION['voter_id'])): ?>
        <form method="POST" class="w3-container w3-card-4 w3-padding" style="max-width: 500px;">
            <label>Voter ID:</label>
            <input class="w3-input w3-border" type="number" name="voterID" required>
            <label>Password:</label>
            <input class="w3-input w3-border" type="password" name="voterPass" required>
            <button type="submit" name="login" class="w3-button w3-blue w3-margin-top">Login</button>
        </form>
    <?php else: ?>
        <p>Welcome, <?php echo $_SESSION['voter_name']; ?>! <a href="?logout">Logout</a></p>
        
        <form method="POST">
            <?php
            $positions = $conn->query("SELECT * FROM positions WHERE posStat='active'");
            while ($pos = $positions->fetch_assoc()):
                $posID = $pos['posID'];
                $candidates = $conn->query("SELECT * FROM candidates WHERE posID=$posID AND candStat='active'");
            ?>
                <div class="w3-container w3-card-4 w3-margin-bottom w3-padding">
                    <h3><?php echo $pos['posName']; ?></h3>
                    <p>Select up to <?php echo $pos['posNumPositions']; ?> candidate(s)</p>
                    <?php while ($cand = $candidates->fetch_assoc()): ?>
                        <label>
                            <?php if ($pos['posNumPositions'] > 1): ?>
                                <input type="checkbox" name="candidate_<?php echo $posID; ?>[]" value="<?php echo $cand['candID']; ?>">
                            <?php else: ?>
                                <input type="radio" name="candidate_<?php echo $posID; ?>" value="<?php echo $cand['candID']; ?>">
                            <?php endif; ?>
                            <?php echo $cand['candFName'] . ' ' . $cand['candLName']; ?>
                        </label><br>
                    <?php endwhile; ?>
                </div>
            <?php endwhile; ?>
            <button type="submit" name="submit_vote" class="w3-button w3-green" onclick="return confirm('Submit your vote?')">Submit Vote</button>
        </form>
    <?php endif; ?>
</body>
</html>