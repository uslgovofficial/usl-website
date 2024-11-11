<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML>
<HEAD>
<TITLE>PHPBank - Administration - Log in</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>
<?php
require "config.php";
$mysqli = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// No need to select the database separately, it's already selected in the constructor
// mysql_select_db($datname,$mysqli); // Remove this line

//get Other information
$query="SELECT * FROM `phpb_other`";
$result=$mysqli->query($query);
$other=$result->fetch_array();
?>
<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>".$other['nation']." - PHPBank: Administration - Log in</h1>";
?>

<form method=POST action="admincp.php">
Admin Password:<br>
<input type=password name="apassword" value=""><br><br>
<input type=submit value="Log in">
</form><br>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>

