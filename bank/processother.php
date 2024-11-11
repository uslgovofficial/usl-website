<?php
session_start();
$apassword = $_SESSION['apassword'];
?>

<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>PHPBank - Administration - Process layout change</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel="stylesheet" type="text/css" href="style.php">
</HEAD>

<?php
require "config.php";

// Create connection
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get other information
$query = "SELECT * FROM phpb_other";
$result = $conn->query($query);
$other = $result->fetch_assoc();

if (isset($_POST['topimg'], $_POST['nation'], $_POST['intromsg'], $_POST['currencysymbol'], $_POST['adminpassword'])) {
    $topimg = filter_input(INPUT_POST, 'topimg', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nation = filter_input(INPUT_POST, 'nation', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $intromsg = filter_input(INPUT_POST, 'intromsg', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $currencysymbol = filter_input(INPUT_POST, 'currencysymbol', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $adminpassword = $_POST['adminpassword'];

    if ($apassword == $other['apassword']) {
        $query = "UPDATE phpb_other SET topimg=?, nation=?, intromessage=?, currencysymbol=?, apassword=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $topimg, $nation, $intromsg, $currencysymbol, $adminpassword);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "The information was successfully updated. If you have changed the password, you will have to log out now and log in again with the new password, or you will receive wrong password errors.<br><br>";
            echo "<a href=\"admincp.php\">Admin CP</a> - <a href=\"alogout.php\">Log out</a>";
        } else {
            die("<h3>ERROR: The information was not changed. Please try again later.</h3>");
        }
    } else {
        echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
        session_destroy();
    }
}
?>

<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>" . $other['nation'] . " - PHPBank: Process other information edit</h1>";
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>