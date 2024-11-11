<?php
session_start();
$apassword = $_SESSION['apassword'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Administration - Processing forced transaction</TITLE>
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

// Get Other information
$query = "SELECT * FROM phpb_other";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

if (isset($_POST['fromaccount'], $_POST['toaccount'], $_POST['amount'], $_POST['comments'])) {
    $fromaccount = filter_input(INPUT_POST, 'fromaccount', FILTER_SANITIZE_NUMBER_INT);
    $toaccount = filter_input(INPUT_POST, 'toaccount', FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check
    if (!$amount) {
        die('<h3>ERROR: No amount specified!</h3><a href="admincp.php">Admin CP</a>');
    }

    if (!preg_match('/^[0-9\.]+$/', $amount)) {
        die('<h3>ERROR: Please use only numbers in the amount, and "." for decimals.</h3><a href="admincp.php">Admin CP</a>');
    }

    if ($amount == 0) {
        die('<h3>ERROR: The amount may not be 0.</h3><a href="admincp.php">Admin CP</a>');
    }

    if (strlen($comments) > 30) {
        die('<h3>ERROR: The comment is longer than 30 characters. Please limit its length to 30 at max.</h3><a href="admincp.php">Admin CP</a>');
    }

    $comment = "FORCED:" . $comments;

    if ($fromaccount == $toaccount) {
        die('<h3>ERROR: Sender and receiver of the money are the same!</h3><a href="admincp.php">Admin CP</a>');
    }

    // Get Account Information
    $query = "SELECT * FROM phpb_accounts WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $fromaccount);
    $stmt->execute();
    $result = $stmt->get_result();
    $fromaccountinfo = $result->fetch_assoc();

    $query = "SELECT * FROM phpb_accounts WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $toaccount);
    $stmt->execute();
    $result = $stmt->get_result();
    $toaccountinfo = $result->fetch_assoc();

    // Amount may not be bigger than the balance of the sender
    if ($amount > $fromaccountinfo['balance']) {
        die('<h3>ERROR: The sender does not have this amount of money. Please try again when he has.</h3><a href="admincp.php">Admin CP</a>');
    }

    // Calculate new balances
    $newfromamount = $fromaccountinfo['balance'] - $amount;
    $newtoamount = $toaccountinfo['balance'] + $amount;

    // Store new balances
    $query = "UPDATE phpb_accounts SET balance=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ds", $newfromamount, $fromaccount);
    $stmt->execute();

    if (!$stmt) {
        die('<h3>ERROR: There is something wrong with the database: the balances did not get stored. Please try again later.</h3><a href="admincp.php">Admin CP</a>');
    }

    $query = "UPDATE phpb_accounts SET balance=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ds", $newtoamount, $toaccount);
    $stmt->execute();

    if (!$stmt) {
        die('<h3>ERROR: There is something wrong with the database: One of the balances did not get stored. This is a serious error! Please contact the development.</h3><a href="admincp.php">Admin CP</a>');
    }

    // Record transaction
    $query = "INSERT INTO phpb_transactions (`requester`,`acceptor`,`direction`,`amount`,`comment`,`status`) VALUES (?, ?, '1', ?, ?, '1')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $fromaccount, $toaccount, $amount, $comment, time());
    $stmt->execute();

    // Update stats
    $query = "UPDATE phpb_other SET transactions=transactions+1, latesttransaction=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", time());
    $stmt->execute();
    ?>
    <h2>The transaction was FORCED.</h2>
    <a href="admincp.php">Admin CP</a>

<?php
} else {
    echo "<h3>ERROR: Account not found. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
}
?>
<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>" . $other['nation'] . " - PHPBank: Processing forced transaction</h1>";
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>