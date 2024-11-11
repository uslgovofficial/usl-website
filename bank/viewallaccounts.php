<?php
session_start();
$apassword = filter_input(INPUT_SESSION, 'apassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
$other['topimg'] = filter_var($other['topimg'], FILTER_SANITIZE_URL);
$other['nation'] = filter_var($other['nation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$other['apassword'] = filter_var($other['apassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Administration - View all accounts</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel="stylesheet" type="text/css" href="style.php">
</HEAD>

<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo htmlspecialchars($other['topimg']); ?>">
<?php
echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Administration - View all accounts</h1>";

//Check if the password is okay+activity
if ($apassword == $other['apassword'])
{
?>
<table cellspacing="1" cellpadding="5"><tr><td>Login</td><td>Name</td><td>Description</td><td>Balance</td><td>Active</td><td>Email</td></tr>
    <?php
    $query = "SELECT * FROM phpb_accounts ORDER BY login ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($displayaccount = $result->fetch_assoc())
    {
        if ($displayaccount['active'])
        {$active="Yes";}
        else
        {$active="No";}
        echo "<tr><td>" . htmlspecialchars($displayaccount['login']);
        echo "</td><td>" . htmlspecialchars($displayaccount['name']);
        echo "</td><td>" . htmlspecialchars($displayaccount['description']);
        echo "</td><td>" . htmlspecialchars($other['currencysymbol']) . " " . htmlspecialchars($displayaccount['balance']);
        echo "</td><td>" . htmlspecialchars($active);
        echo "</td><td>" . htmlspecialchars($displayaccount['email']);
        echo "</td></tr>";
    }
    ?>
</table>
<br>
<a href="admincp.php">Admin CP</a>
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