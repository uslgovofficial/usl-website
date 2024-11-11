<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML>
<HEAD>
<TITLE>PHPBank - Log in</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>
<?php
require "config.php";

// Create a new mysqli object
$mysqli = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

//get Other information
$query="SELECT * FROM `phpb_other`";
$result=$mysqli->query($query);
$other=$result->fetch_assoc();

?>
<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>".$other['nation']." - PHPBank: Log in</h1>";
?>
<form method=POST action="usercp.php">
Login ID:<br>
<input type=text name="login" value=""><br><br>
Password:<br>
<input type=password name="password" value=""><br><br>
<input type=submit value="Log in">
</form><br><br>
<a href="index.php">Index</a>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>