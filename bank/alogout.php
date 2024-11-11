<?php
session_start();

// Check if session variables are set
if (!isset($_SESSION['apassword'])) {
    // Redirect to login page if session variables are not set
    header('Location: login.php');
    exit;
}

// Get session variables
$apassword = $_SESSION['apassword'];

// Sanitize and validate session variables
$apassword = filter_var($apassword, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if session variables are valid
if (empty($apassword)) {
    // Redirect to login page if session variables are invalid
    header('Location: login.php');
    exit;
}

// Destroy session
session_destroy();

require "config.php";

// Create connection
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get other information
$query = "SELECT * FROM phpb_other";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

// Sanitize and validate other information
$other['topimg'] = filter_var($other['topimg'], FILTER_VALIDATE_URL);
$other['nation'] = filter_var($other['nation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPBank - Administration - Logging out</title>
    <link rel="stylesheet" type="text/css" href="style.php">
</head>

<body>
    <table height="100%" width="100%" class="centertable">
        <tr>
            <td class="centertable" height="100%" width="100%" valign="middle" align="center">
                <table cellpadding="15">
                    <tr>
                        <td align="center">
                            <img src="<?php echo htmlspecialchars($other['topimg']); ?>">
                            <?php
                            echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Administration - Logging out</h1>";
                            ?>
                            <h2>You are now logged out of the Admin CP.</h2>
                            <a href="index.php">Index</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>