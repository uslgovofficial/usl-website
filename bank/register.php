<?php
session_start();
session_destroy();

require "config.php";

$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Get Other information
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
    <title>PHPBank - Register an account</title>
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
                            echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Register an account</h1>";
                            ?>
                            To register your own bank account, fill out this form.<br>
                            Your account will not be usable right away, it needs to be activated first.<br>
                            You will be notified when the account has been activated.<br><br>
                            <form name="register" method="POST" action="processregister.php">
                                Account Name:<br><input type="text" name="name" value=""><br><br>
                                Login:<br><input type="text" name="login" value=""><br>Only letters! Case insensitive.<br><br>
                                Your desired password:<br><input type="password" name="password1" value=""><br><br>
                                Your password again:<br><input type="password" name="password2" value=""><br><br>
                                Passwords are case sensitive, and cannot be retrieved!<br><br>
                                Account Description:<br><textarea name="description"></textarea><br>Not required.<br><br>
                                Your email:<br><input type="email" name="email" value=""><br><br>
                                <input type="submit" value="send request"><input type="reset" value="reset">
                                <br><br>
                            </form>
                            <a href="index.php">Index</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>