<?php
session_start();
$apassword = $_SESSION['apassword'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Administration - Activate account</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>

<?php
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
?>

<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>" . $other['nation'] . " - PHPBank: Administration - Activating account</h1>";

//Check if the password is okay+activity
if ($apassword == $other['apassword']) {
    $accountid = $_POST['accountid'];
    $query = "SELECT * FROM phpb_accounts WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountid);
    $stmt->execute();
    $result = $stmt->get_result();
    $accountinfo = $result->fetch_assoc();

    $query = "UPDATE phpb_accounts SET active=1 WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountid);
    $stmt->execute();

    if (!$stmt->affected_rows) {
        die ("<h3>ERROR: The account couldn't be activated. Please try again later.</h3>");
    }

    echo "The account was activated.<br><br>";
    echo "<a href='admincp.php'>Admin CP</a>";

} else {
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
}
?>

</td></tr></table>
</td></tr></table>
</BODY>
</HTML>
