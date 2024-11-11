<?php
require "config.php";

$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = array('phpb_accounts', 'phpb_layout', 'phpb_other', 'phpb_transactions');
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        header('Location: /install.php');
        exit;
    }
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
$other['intromessage'] = filter_var($other['intromessage'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$other['accounts'] = filter_var($other['accounts'], FILTER_VALIDATE_INT);
$other['transactions'] = filter_var($other['transactions'], FILTER_VALIDATE_INT);
$other['latesttransaction'] = filter_var($other['latesttransaction'], FILTER_VALIDATE_INT);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Main</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel="stylesheet" type="text/css" href="style.php">
</HEAD>

<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center" colspan="2">
<img src="<?php echo htmlspecialchars($other['topimg']); ?>">
<?php
echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank</h1>";
echo htmlspecialchars($other['intromessage']);
?>
</td></tr><tr><td align="center">
<h2>Stats</h2>
<?php
echo "Accounts: " . htmlspecialchars($other['accounts']) . "<br>";
echo "Transactions: " . htmlspecialchars($other['transactions']) . "<br>";
if ($other['latesttransaction']) {
    $transdate = date("D j F, g:i A", strtotime($other['latesttransaction']));
    echo "Latest transaction:<br>" . htmlspecialchars($transdate) . "<br>";
} else {
    echo "Latest transaction:<br>No transactions yet<br>";
}
?>
Version: v2.0b<br>
</td><td align="center">
<a href="login.php">Log in</a><br>
<a href="register.php">Register an account</a><br>
<a href="alogin.php">Administration</a>
</td></tr></table>
<div style="font-size: 8px;">
    Copyright NeoBank - The code is licensed under MIT and is original from the Free Republic of Embau, based on PHPBank.
    <br>
    PHPBank by <a href="mailto:sanderdieleman[at]hotmail[dot]com">Sander Dieleman</a>, 2004.
</div>
</td></tr></table>
</BODY>
</HTML>