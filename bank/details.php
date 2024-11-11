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

// Check if session variables are valid
if (empty($login) || empty($password)) {
    // Redirect to login page if session variables are invalid
    header('Location: login.php');
    exit;
}

// Check if the password is correct
require "config.php";

$db = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get account info
$query = "SELECT * FROM phpb_accounts where login=? AND password=?";
$stmt = $db->prepare($query);
$stmt->bind_param("ss", $login, $password);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_array();

// Check if account exists
if (!$account) {
    // Redirect to login page if account does not exist
    header('Location: login.php');
    exit;
}

// Get other information
$query = "SELECT * FROM phpb_other";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_array();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Edit account details</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>

<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>" . $other['nation'] . " - PHPBank: Edit account details</h1>";

if ($account['active']) {
    ?>
    <form method="POST" name="accountdetails" action="processdetails.php">
        Account Name:<br>
        <input type="text" name="name" value="<?php echo $account['name']; ?>"><br><br>
        New Password:<br>
        <input type="password" name="password1" value=""><br><br>
        New Password again:<br>
        <input type="password" name="password2" value=""><br><br>
        Description:<br>
        <textarea name="description"><?php echo $account['description']; ?></textarea><br><br>
        Email:<br>
        <input type="text" name="email" value="<?php echo $account['email']; ?>"><br><br>

        <input type="submit" value="Change details">
    </form><br>
    <a href="usercp.php">User CP</a>

    <?php
} else {
    echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
    session_destroy();
}
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>