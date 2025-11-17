<?php 
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Add new positions
        $posName = $_POST['posName'];
        $posNumPositions = $_POST['posNumPositions'];
        $sql = "INSERT INTO positions (posName, posNumPositions, posStat) VALUES ('$posName', $posNumPositions, 'active')";
        $conn->query($sql);
    }
    elseif (isset($_POST['edit'])) {
        // Edit/Update positions
        $id = $_POST['id'];
        $posName = $_POST['posName'];
        $posNumPositions = $_POST['posNumPositions'];
        $sql = "UPDATE positions SET posName='$posName', posNumPositions=$posNumPositions WHERE posID=$id";
        $conn->query($sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Handles the  deactivation
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $sql = "UPDATE positions SET posStat='inactive' WHERE posID=$id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handles the activation
if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $sql = "UPDATE positions SET posStat='inactive' WHERE posID=$id";
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
    <title>Position Management</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container">
    <h2 class="w3-text-blue">Position Management</h2>
    
    <div class="w3-bar w3-border w3-light-grey w3-margin-bottom">
        <a href="index.php" class="w3-bar-item w3-button w3-green">Home</a>
    </div>
</body>
</html>