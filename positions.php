<?php
require_once 'db_connection.php';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Add new position
        $posName = $_POST['posName'];
        $posNumPositions = $_POST['posNumPositions'];
        $sql = "INSERT INTO positions (posName, posNumPositions, posStat) VALUES ('$posName', $posNumPositions, 'active')";
        $conn->query($sql);
    }
    elseif (isset($_POST['edit'])) {
        // Update position
        $id = $_POST['id'];
        $posName = $_POST['posName'];
        $posNumPositions = $_POST['posNumPositions'];
        $sql = "UPDATE positions SET posName='$posName', posNumPositions=$posNumPositions WHERE posID=$id";
        $conn->query($sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deactivate
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $sql = "UPDATE positions SET posStat='inactive' WHERE posID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle activate
if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $sql = "UPDATE positions SET posStat='active' WHERE posID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all positions
$result = $conn->query("SELECT * FROM positions");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Positions Management</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Positions Management</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button">Home</a>
        <a href="positions.php" class="w3-bar-item w3-button w3-green">Positions</a>
        <a href="candidates.php" class="w3-bar-item w3-button">Candidates</a>
        <a href="voters.php" class="w3-bar-item w3-button">Voters</a>
        <a href="vote.php" class="w3-bar-item w3-button">Vote</a>
        <a href="results.php" class="w3-bar-item w3-button">Results</a>
        <a href="winners.php" class="w3-bar-item w3-button">Winners</a>
    </div>
    
    <!-- Add/Edit Form -->
    <form method="POST" class="w3-container w3-card-4 w3-padding">
        <?php if (isset($_GET['edit'])):
            $edit_id = $_GET['edit'];
            $edit_result = $conn->query("SELECT * FROM positions WHERE posID=$edit_id");
            $edit_row = $edit_result->fetch_assoc();
        ?>
            <input type="hidden" name="id" value="<?php echo $edit_row['posID']; ?>">
            <div class="w3-row-padding">
                <div class="w3-col m4">
                    <label>Position Name:</label>
                    <input class="w3-input w3-border" type="text" name="posName" value="<?php echo $edit_row['posName']; ?>" required>
                </div>
                <div class="w3-col m4">
                    <label>Number of Positions:</label>
                    <input class="w3-input w3-border" type="number" name="posNumPositions" value="<?php echo $edit_row['posNumPositions']; ?>" required>
                </div>
            </div>
            <button type="submit" name="edit" class="w3-button w3-blue w3-margin-top">Update Position</button>
        <?php else: ?>
            <div class="w3-row-padding">
                <div class="w3-col m4">
                    <label>Position Name:</label>
                    <input class="w3-input w3-border" type="text" name="posName" required>
                </div>
                <div class="w3-col m4">
                    <label>Number of Positions:</label>
                    <input class="w3-input w3-border" type="number" name="posNumPositions" value="1" required>
                </div>
            </div>
            <button type="submit" name="add" class="w3-button w3-blue w3-margin-top">Add Position</button>
        <?php endif; ?>
    </form>

    <!-- Positions List -->
    <div class="w3 container w3-margin-top">
        <table class="w3-table w3-striped w3-bordered w3-hoverable">
            <tr class="w3-green">
                <th>Position Name</th>
                <th>Number of Positions</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['posName']; ?></td>
                    <td><?php echo $row['posNumPositions']; ?></td>
                    <td><?php echo $row['posStat']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['posID']; ?>" class="w3-button w3-orange">Edit</a>
                        <?php if ($row['posStat'] == 'active'): ?>
                            <a href="?deactivate=<?echo $row['posID']; ?>" class="w3-button w3-red" onclick="return confirm('Are you sure?')">Deactivate</a>
                        <?php else: ?>
                            <a href="?activate=<?echo $row['posID']; ?>" class="w3-button w3-green">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>
    </div>
</body>
</html>