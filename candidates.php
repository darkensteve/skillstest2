<?php
require_once 'db_connection.php';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Add new candidate
        $candFName = $_POST['candFName'];
        $candMName = $_POST['candMName'];
        $candLName = $_POST['candLName'];
        $posID = $_POST['posID'];
        $sql = "INSERT INTO candidates (candFName, candMName, candLName, posID, candStat) VALUES ('$candFName', '$candMName', '$candLName', $posID, 'active')";
        $conn->query($sql);
    }
    elseif (isset($_POST['edit'])) {
        // Update candidate
        $id = $_POST['id'];
        $candFName = $_POST['candFName'];
        $candMName = $_POST['candMName'];
        $candLName = $_POST['candLName'];
        $posID = $_POST['posID'];
        $sql = "UPDATE candidates SET candFName='$candFName', candMName='$candMName', candLName='$candLName', posID=$posID WHERE candID=$id";
        $conn->query($sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deactivate
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $sql = "UPDATE candidates SET candStat='inactive' WHERE candID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle activate
if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $sql = "UPDATE candidates SET candStat='active' WHERE candID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all candidates
$result = $conn->query("SELECT c.*, p.posName FROM candidates c LEFT JOIN positions p ON c.posID = p.posID");
$positions = $conn->query("SELECT * FROM positions");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Candidates Management</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Candidates Management</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button w3-green">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button">Winners</a>
    </div>
    
    <!-- Add/Edit Form -->
    <form method="POST" class="w3-container w3-card-4 w3-padding">
        <?php if (isset($_GET['edit'])):
            $edit_id = $_GET['edit'];
            $edit_result = $conn->query("SELECT * FROM candidates WHERE candID=$edit_id");
            $edit_row = $edit_result->fetch_assoc();
        ?>
            <input type="hidden" name="id" value="<?php echo $edit_row['candID']; ?>">
            <div class="w3-row-padding">
                <div class="w3-col m3">
                    <label>First Name:</label>
                    <input class="w3-input w3-border" type="text" name="candFName" value="<?php echo $edit_row['candFName']; ?>" required>
                </div>
                <div class="w3-col m3">
                    <label>Middle Name:</label>
                    <input class="w3-input w3-border" type="text" name="candMName" value="<?php echo $edit_row['candMName']; ?>">
                </div>
                <div class="w3-col m3">
                    <label>Last Name:</label>
                    <input class="w3-input w3-border" type="text" name="candLName" value="<?php echo $edit_row['candLName']; ?>" required>
                </div>
                <div class="w3-col m3">
                    <label>Position:</label>
                    <select class="w3-select w3-border" name="posID" required>
                        <?php 
                        $positions->data_seek(0);
                        while($pos = $positions->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $pos['posID']; ?>" <?php echo $edit_row['posID'] == $pos['posID'] ? 'selected' : ''; ?>><?php echo $pos['posName']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="edit" class="w3-button w3-blue w3-margin-top">Update Candidate</button>
        <?php else: ?>
            <div class="w3-row-padding">
                <div class="w3-col m3">
                    <label>First Name:</label>
                    <input class="w3-input w3-border" type="text" name="candFName" required>
                </div>
                <div class="w3-col m3">
                    <label>Middle Name:</label>
                    <input class="w3-input w3-border" type="text" name="candMName">
                </div>
                <div class="w3-col m3">
                    <label>Last Name:</label>
                    <input class="w3-input w3-border" type="text" name="candLName" required>
                </div>
                <div class="w3-col m3">
                    <label>Position:</label>
                    <select class="w3-select w3-border" name="posID" required>
                        <option value="">Select Position</option>
                        <?php 
                        $positions->data_seek(0);
                        while($pos = $positions->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $pos['posID']; ?>"><?php echo $pos['posName']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="add" class="w3-button w3-blue w3-margin-top">Add Candidate</button>
        <?php endif; ?>
    </form>

    <!-- Candidates List -->
    <div class="w3-container w3-margin-top">
        <table class="w3-table w3-striped w3-bordered w3-hoverable">
            <tr class="w3-green">
                <th>Full Name</th>
                <th>Position</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['candFName'] . ' ' . $row['candMName'] . ' ' . $row['candLName']; ?></td>
                    <td><?php echo $row['posName']; ?></td>
                    <td><?php echo $row['candStat']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['candID']; ?>" class="w3-button w3-orange">Edit</a>
                        <?php if ($row['candStat'] == 'active'): ?>
                            <a href="?deactivate=<?php echo $row['candID']; ?>" class="w3-button w3-red" onclick="return confirm('Are you sure?')">Deactivate</a>
                        <?php else: ?>
                            <a href="?activate=<?php echo $row['candID']; ?>" class="w3-button w3-green">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>