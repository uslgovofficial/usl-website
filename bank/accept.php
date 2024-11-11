<?php
session_start();
$login = $_SESSION['login'];
$password = $_SESSION['password'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Accepting transaction</TITLE>
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
$accountid = $account['id'];

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
echo "<h1>" . $other['nation'] . " - PHPBank: Accepting transaction</h1>";

//Check if the password is okay+activity
if ($password == $account['password']) {
    if ($account['active']) {
        //Get Transaction Information
        $transactionid = $_POST['transactionid'];
        $query = "SELECT * FROM phpb_transactions where id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $transactionid);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();

        //Check if transactions hasn't been deleted in the meantime
        if (!$transaction) {
            die('<h3>ERROR: This transaction no longer exists. It was probably cancelled.</h3><a href="usercp.php">User CP</a>');
        }
        //Else, continue to checking if transaction is still pending
        if ($transaction['status']) {
            die('This transaction is no longer pending. It may have been accepted or denied in the meantime.');
        }

        //Get Requester Account Information
        $RQid = $transaction['requester'];
        $query = "SELECT * FROM phpb_accounts WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $RQid);
        $stmt->execute();
        $result = $stmt->get_result();
        $RQaccount = $result->fetch_assoc();

        //actual transaction
        $direction = $transaction['direction'];
        //if $direction=1, then the direction is RQ->AC send money; if it is 0, the direction is AC->RQ request money
        if ($direction) {
            //amount may not be bigger than the balance of the requester
            if ($transaction['amount'] > $RQaccount['balance']) {
                die('<h3>ERROR: The transaction requester does not have this amount of money. Please try again when he has, or deny the transaction.</h3><a href="usercp.php">User CP</a>');
            }

            //calculate new balances
            $newRQamount = $RQaccount['balance'] - $transaction['amount'];
            $newACamount = $account['balance'] + $transaction['amount'];
        } else {
            //amount may not be bigger than the balance of the acceptor
            if ($transaction['amount'] > $account['balance']) {
                die('<h3>ERROR: You do not have this amount of money. Please try again when you have, or deny the transaction.</h3><a href="usercp.php">User CP</a>');
            }

            //calculate new balances
            $newRQamount = $RQaccount['balance'] + $transaction['amount'];
            $newACamount = $account['balance'] - $transaction['amount'];
        }

        //store new balances
        $query = "UPDATE phpb_accounts SET balance=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $newRQamount, $RQid);
        $stmt->execute();

        if (!$stmt->affected_rows) {
            die('<h3>ERROR: There is something wrong with the database: the new balances did not get stored. Please try again later.</h3><a href="usercp.php">User CP</a>');
        }

        $query = "UPDATE phpb_accounts SET balance=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $newACamount, $accountid);
        $stmt->execute();

        if (!$stmt->affected_rows) {
            die('<h3>ERROR: There is something wrong with the database: the new balances did not get stored. Please try again later.</h3><a href="usercp.php">User CP</a>');
        }

        //Edit Transaction status to accepted + timestamp
        $timestamp = time();
        $query = "UPDATE phpb_transactions SET status='1', timestamp=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $timestamp, $transactionid);
        $stmt->execute();

        if (!$stmt->affected_rows) {
            die('<h3>ERROR: There is something wrong with the database: the transaction status did not change! This is a serious error; please report this to the PHPBank admin and the development team.</h3><a href="usercp.php">User CP</a>');
        }

        //update stats
        $query = "UPDATE phpb_other SET transactions=transactions+1, latesttransaction=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $timestamp);
        $stmt->execute();

        ?>
        <h2>The transaction was ACCEPTED.</h2>
        <a href="usercp.php">User CP</a>
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
