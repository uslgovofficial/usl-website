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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Logging out</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel="stylesheet" type="text/css" href="style.php">
</HEAD>

<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo htmlspecialchars($other['topimg']); ?>">
<?php
echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Logging out</h1>";
?>
<h2>You are now logged out.</h2>
<a href="index.php">Index</a>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>