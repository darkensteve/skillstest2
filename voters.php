<?php
require_once 'db_connection.php';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Add new voter
        $voterID = $_POST['voterID'];
        $voterPass = $_POST['voterPass'];
        $voterFName = $_POST['voterFName'];
        $voterMName = $_POST['voterMName'];
        $voterLName = $_POST['voterLName'];
        $sql = "INSERT INTO voters (voterID, voterPass, voterFName, voterMName, voterLName, voterStat, voted) VALUES ($voterID, '$voterPass', '$voterFName', '$voterMName', '$voterLName', 'active', 'n')";
        $conn->query($sql);
    }
    elseif (isset($_POST['edit'])) {
        // Update voter
        $id = $_POST['id'];
        $voterPass = $_POST['voterPass'];
        $voterFName = $_POST['voterFName'];
        $voterMName = $_POST['voterMName'];
        $voterLName = $_POST['voterLName'];
        $sql = "UPDATE voters SET voterPass='$voterPass', voterFName='$voterFName', voterMName='$voterMName', voterLName='$voterLName' WHERE voterID=$id";
        $conn->query($sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deactivate
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $sql = "UPDATE voters SET voterStat='inactive' WHERE voterID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle activate
if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $sql = "UPDATE voters SET voterStat='active' WHERE voterID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all voters
$result = $conn->query("SELECT * FROM voters");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Voters Management</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Voters Management</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button w3-green">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button">Winners</a>
    </div>
    
    <!-- Add/Edit Form -->
    <form method="POST" class="w3-container w3-card-4 w3-padding">
        <?php if (isset($_GET['edit'])):
            $edit_id = $_GET['edit'];
            $edit_result = $conn->query("SELECT * FROM voters WHERE voterID=$edit_id");
            $edit_row = $edit_result->fetch_assoc();
        ?>
            <input type="hidden" name="id" value="<?php echo $edit_row['voterID']; ?>">
            <div class="w3-row-padding">
                <div class="w3-col m2">
                    <label>Voter ID:</label>
                    <input class="w3-input w3-border w3-light-grey" type="text" value="<?php echo $edit_row['voterID']; ?>" readonly>
                </div>
                <div class="w3-col m2">
                    <label>Password:</label>
                    <input class="w3-input w3-border" type="text" name="voterPass" value="<?php echo $edit_row['voterPass']; ?>" required>
                </div>
                <div class="w3-col m3">
                    <label>First Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterFName" value="<?php echo $edit_row['voterFName']; ?>" required>
                </div>
                <div class="w3-col m2">
                    <label>Middle Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterMName" value="<?php echo $edit_row['voterMName']; ?>">
                </div>
                <div class="w3-col m3">
                    <label>Last Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterLName" value="<?php echo $edit_row['voterLName']; ?>" required>
                </div>
            </div>
            <button type="submit" name="edit" class="w3-button w3-blue w3-margin-top">Update Voter</button>
        <?php else: ?>
            <div class="w3-row-padding">
                <div class="w3-col m2">
                    <label>Voter ID:</label>
                    <input class="w3-input w3-border" type="number" name="voterID" required>
                </div>
                <div class="w3-col m2">
                    <label>Password:</label>
                    <input class="w3-input w3-border" type="text" name="voterPass" required>
                </div>
                <div class="w3-col m3">
                    <label>First Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterFName" required>
                </div>
                <div class="w3-col m2">
                    <label>Middle Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterMName">
                </div>
                <div class="w3-col m3">
                    <label>Last Name:</label>
                    <input class="w3-input w3-border" type="text" name="voterLName" required>
                </div>
            </div>
            <button type="submit" name="add" class="w3-button w3-blue w3-margin-top">Add Voter</button>
        <?php endif; ?>
    </form>

    <!-- Voters List -->
    <div class="w3-container w3-margin-top">
        <table class="w3-table w3-striped w3-bordered w3-hoverable">
            <tr class="w3-green">
                <th>Voter ID</th>
                <th>Full Name</th>
                <th>Password</th>
                <th>Status</th>
                <th>Voted</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['voterID']; ?></td>
                    <td><?php echo $row['voterFName'] . ' ' . $row['voterMName'] . ' ' . $row['voterLName']; ?></td>
                    <td><?php echo $row['voterPass']; ?></td>
                    <td><?php echo $row['voterStat']; ?></td>
                    <td><?php echo $row['voted']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['voterID']; ?>" class="w3-button w3-orange">Edit</a>
                        <?php if ($row['voterStat'] == 'active'): ?>
                            <a href="?deactivate=<?php echo $row['voterID']; ?>" class="w3-button w3-red" onclick="return confirm('Are you sure?')">Deactivate</a>
                        <?php else: ?>
                            <a href="?activate=<?php echo $row['voterID']; ?>" class="w3-button w3-green">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>