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
$other['currencysymbol'] = filter_var($other['currencysymbol'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Administration - View all transactions</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel="stylesheet" type="text/css" href="style.php">
</HEAD>

<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo htmlspecialchars($other['topimg']); ?>">
<?php
echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Administration - View all transactions</h1>";

//Check if the password is okay+activity
if ($apassword == $other['apassword'])
{
?>
<table cellspacing="1" cellpadding="5"><tr><td>Requester</td><td>Direction</td><td>Acceptor</td><td>Amount</td><td>Comment</td><td>Status</td><td>Time</td></tr>
    <?php
    $query = "SELECT * FROM phpb_transactions ORDER BY timestamp DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($transactions = $result->fetch_assoc())
    {
        //get RQlogin
        $RQid = $transactions['requester'];
        $query = "SELECT * FROM phpb_accounts WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $RQid);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $RQaccount = $result2->fetch_assoc();
        $RQlogin = $RQaccount['login'];
        
        //get AClogin
        $ACid = $transactions['acceptor'];
        $query = "SELECT * FROM phpb_accounts WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ACid);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $ACaccount = $result2->fetch_assoc();
        $AClogin = $ACaccount['login'];
        
        //direction
        if ($transactions['direction'])
        {$direction="-->";
        }
        else
        {$direction="<--";}
        
        //status
        if ($transactions['status']=="2")
        {$status="Denied";}
        if ($transactions['status']=="1")
        {$status="Accepted";}
        if ($transactions['status']=="0")
        {$status="Pending";}
        
        //time
        if($transactions['timestamp'])
        {$datetime=date ("D j F, g:i A", $transactions['timestamp']);}
        else
        {$datetime="N/A";}
        
        //echo
        echo "<tr><td>" . htmlspecialchars($RQlogin);
        echo "</td><td>" . htmlspecialchars($direction);
        echo "</td><td>" . htmlspecialchars($AClogin);
        echo "</td><td>" . htmlspecialchars($other['currencysymbol']) . " " . htmlspecialchars($transactions['amount']);
        echo "</td><td>" . htmlspecialchars($transactions['comment']);
        echo "</td><td>" . htmlspecialchars($status);
        echo "</td><td>" . htmlspecialchars($datetime);
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