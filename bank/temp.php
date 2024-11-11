<?php
session_start();

// Check if session variables are set
if (!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
    // Redirect to login page if session variables are not set
    header('Location: login.php');
    exit;
}

// Get session variables
$login = $_SESSION['login'];
$password = $_SESSION['password'];

// Sanitize and validate session variables
$login = filter_var($login, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

require "config.php";

// Create connection
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Account Information
$query = "SELECT * FROM phpb_accounts WHERE login=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

// Check if password is correct
if (password_verify($password, $account['password'])) {
    // Check if account is active
    if ($account['active']) {
        // Account is active, display account information
        echo "<h1>Account Information</h1>";
        echo "<p>Login: " . htmlspecialchars($account['login']) . "</p>";
        echo "<p>Password: " . htmlspecialchars($account['password']) . "</p>";
    } else {
        // Account is not active, display error message
        echo "<h1>Error</h1>";
        echo "<p>Your account is not active.</p>";
        session_destroy();
    }
} else {
    // Password is incorrect, display error message
    echo "<h1>Error</h1>";
    echo "<p>Wrong password.</p>";
    session_destroy();
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
    <title>Untitled</title>
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
                            echo "<h1>" . htmlspecialchars($other['nation']) . "</h1>";
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>