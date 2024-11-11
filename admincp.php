<?php
session_start();
require "config.php";

// Check if admin password is set
if (!isset($_SESSION['apassword'])) {
   $apassword = $_POST['apassword'];
   $_SESSION['apassword'] = $apassword;
} else {
   $apassword = $_SESSION['apassword'];
}

// Connect to database using mysqli
$mysqli = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($mysqli->connect_error) {
   die("Connection failed: " . $mysqli->connect_error);
}

// Get other information
$query = "SELECT * FROM phpb_other";
$result = $mysqli->query($query);
$other = $result->fetch_array();

?>

<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>Administration CP</h1>";
//Check if the password is okay
if ($apassword==$other['apassword'])
{
//layout
$query="SELECT * FROM `phpb_layout`";
$result = $mysqli->query($query);
$layout = $result->fetch_array();

?>
</td></tr><tr><td align=center>
<h2>Other Information</h2>
<form name="other" action="processother.php" method="post">
   <table cellspacing=0 cellpadding=0 width=100%><tr><td width=50%>
   Top Image URL<br>
   <input type=text name=topimg value="<?php echo $other['topimg']; ?>">
   </td><td width=50%>
   Nation name<br>
   <input type=text name=nation value="<?php echo $other['nation']; ?>">
   </td></tr><tr><td>
   Index message<br>
   <textarea cols=25 rows=4 name=intromsg><?php echo $other['intromessage']; ?></textarea>
   </td><td>
   Currency Symbol<br>
   <input type=text name=currencysymbol value="<?php echo $other['currencysymbol']; ?>">
   </td></tr><tr><td>
   Admin Password<br>
   <input type=text name=adminpassword value="<?php echo $other['apassword']; ?>">
   </td><td>
   <input type=submit value="Edit other information">
   </td></tr></table>
</form>
</td></tr><tr><td align=center>
<h2>Account activation</h2>
Over here, the accounts that are awaiting activation can be activated.<br>
<table width="100%" cellspacing="1">
    <tr><td>Login</td><td>Name</td><td>Email</td><td>Description</td><td>Activate</td></tr>
    <?php
    $query = "SELECT * FROM phpb_accounts WHERE active='0'";
    $result = $mysqli->query($query);
    while ($activateaccount = $result->fetch_array()) {
        echo "<tr><td>" . $activateaccount['login'] . "</td><td>" . $activateaccount['name'] . "</td><td>" . $activateaccount['email'] . "</td><td>" . $activateaccount['description'] . "</td><td>";
        echo "<form style='display: inline;' action='activate.php' method='post'><input type='hidden' name='accountid' value='" . $activateaccount['id'] . "'><input type='submit' value='Activate'></form>";
        echo "<form style='display: inline;' action='delete.php' method='post'><input type='hidden' name='accountid' value='" . $activateaccount['id'] . "'><input type='submit' value='Delete'></form>";
    }
    ?>
</table>
</td></tr><tr><td align=center>
<h2>Force a transaction</h2>
<form name="forcetransaction" action="processforced.php" method="post">
	<table width=100% cellspacing=0><tr><td width=50%>
	Transfer money from:<br>
	<select name=fromaccount>
	<?php
   $query = "SELECT * FROM `phpb_accounts`";
   $result = $mysqli->query($query);
   while ($fromaccount = $result->fetch_array()) {
		echo "<option value='".$fromaccount['id']."'>".$fromaccount['login'];
	}
	?>
	</select>
	</td><td width=50%>
	To:<br>
	<select name=toaccount>
	<?php
   $query = "SELECT * FROM `phpb_accounts`";
   $result = $mysqli->query($query);
   while ($toaccount = $result->fetch_array()) {
		echo "<option value='".$toaccount['id']."'>".$toaccount['login'];
	}
	?>
	</select>
	</td></tr><tr><td>
	Amount (only numbers):<br>
	<?php
	echo $other['currencysymbol']." ";
	?><input type=text name=amount>	
	</td><td>
	Comments:<br>
	<textarea name=comments></textarea>
	</td></tr></table>
   <input type="submit" value="Force transaction">
</form>

</td></tr><tr><td align=center>
<a href="viewallaccounts.php">View all accounts</a> - <a href="viewalltransactions.php">View all transactions</a> - <a href="alogout.php">Log out</a>
<?php
}
else
{
echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
session_destroy();
}
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>