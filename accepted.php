<?php
session_start();
$login=$_SESSION['login'];
$password=$_SESSION['password'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Accepted transactions history</TITLE>
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

//Get Account Information
$query = "SELECT * FROM phpb_accounts where login=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

//get Other information
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
echo "<h1>" . $other['nation'] . " - PHPBank: Accepted transactions history</h1>";

//Check if the password is okay+activity
if ($password == $account['password']) {
    if ($account['active']) {
        $accountid = $account['id'];
        ?>
        <h2>Accepted by you:</h2>
        <table cellpadding=5 cellspacing=1><tr>
        <td>Requester</td><td>Direction</td><td>Amount</td><td>Comment</td><td>Date/Time</td>
        </tr>
        <?php
        $query = "SELECT * FROM phpb_transactions WHERE acceptor=? AND status='1' ORDER BY timestamp DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accountid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($transactions = $result->fetch_assoc()) {
            $query = "SELECT * FROM phpb_accounts WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $transactions['requester']);
            $stmt->execute();
            $result2 = $stmt->get_result();
            $requesterarray = $result2->fetch_assoc();
            echo "<tr><td>" . $requesterarray['name'] . "</td>";
            if ($transactions['direction']) {
                $direction = "Incoming";
            } else {
                $direction = "Outgoing";
            }
            echo "<td>" . $direction . "</td>";
            echo "<td>" . $other['currencysymbol'] . " " . $transactions['amount'] . "</td>";
            echo "<td>" . $transactions['comment'] . "</td>";
            $datetime = date("D j F, g:i A", $transactions['timestamp']);
            echo "<td>" . $datetime . "</td></tr>";
        }
        ?>
        </table>

        <h2>Accepted by others:</h2>
        <table cellspacing=1 cellpadding=5><tr>
        <td>Acceptor</td><td>Direction</td><td>Amount</td><td>Comment</td><td>Date/Time</td>
        </tr>
        <?php
        $query = "SELECT * FROM phpb_transactions WHERE requester=? AND status='1' ORDER BY timestamp DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accountid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($transactions = $result->fetch_assoc()) {
            $query = "SELECT * FROM phpb_accounts WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $transactions['acceptor']);
            $stmt->execute();
            $result2 = $stmt->get_result();
            $acceptorarray = $result2->fetch_assoc();
            echo "<tr><td>" . $acceptorarray['name'] . "</td>";
            if ($transactions['direction']) {
                $direction = "Outgoing";
            } else {
                $direction = "Incoming";
            }
            echo "<td>" . $direction . "</td>";
            echo "<td>" . $other['currencysymbol'] . " " . $transactions['amount'] . "</td>";
            echo "<td>" . $transactions['comment'] . "</td>";
            $datetime = date("D j F, g:i A", $transactions['timestamp']);
            echo "<td>" . $datetime . "</td></tr>";
        }
        ?>
        </table>
        <br><br><a href="usercp.php">User CP</a>
        <?php
    } else {
        echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
        session_destroy();
    }
} else {
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
}
?>

</td></tr></table>
</td></tr></table>
</BODY>
</HTML>