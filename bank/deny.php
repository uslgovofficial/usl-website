<?php
session_start();

// Check if session variables are set
if (!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
    // Redirect to login page if session variables are not set
    header('Location: login.php');
    exit;
}

// Get session variables
$login = $_SESSION['login'];
$password = $_SESSION['password'];

// Sanitize and validate session variables
$login = filter_var($login, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if session variables are valid
if (empty($login) || empty($password)) {
    // Redirect to login page if session variables are invalid
    header('Location: login.php');
    exit;
}

// Check if the password is correct
require "config.php";

$db = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get account info
$query = "SELECT * FROM phpb_accounts where login=? AND password=?";
$stmt = $db->prepare($query);
$stmt->bind_param("ss", $login, $password);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_array();

// Check if account exists
if (!$account) {
    // Redirect to login page if account does not exist
    header('Location: login.php');
    exit;
}

// Get other information
$query = "SELECT * FROM phpb_other";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_array();

// Sanitize and validate other information
$other['topimg'] = filter_var($other['topimg'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$other['nation'] = filter_var($other['nation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Denying transaction</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>

<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center>
<img src="<?php echo htmlspecialchars($other['topimg']); ?>">
<?php
echo "<h1>" . htmlspecialchars($other['nation']) . " - PHPBank: Denying transaction</h1>";

// Check if transaction ID is set
if (!isset($_POST['transactionid'])) {
    // Redirect to error page if transaction ID is not set
    header('Location: error.php');
    exit;
}

// Sanitize and validate transaction ID
$transactionid = filter_var($_POST['transactionid'], FILTER_VALIDATE_INT);

// Check if transaction ID is valid
if (!$transactionid) {
    // Redirect to error page if transaction ID is invalid
    header('Location: error.php');
    exit;
}

// Check if transactions hasn't been deleted in the meantime
$query = "SELECT * FROM phpb_transactions WHERE id=?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $transactionid);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_array();

// Check if transaction exists
if (!$transaction) {
    // Redirect to error page if transaction does not exist
    header('Location: error.php');
    exit;
}

// Check if transaction is still pending
if ($transaction['status']) {
    // Redirect to error page if transaction is not pending
    header('Location: error.php');
    exit;
}

// Edit Transaction status to denied
$timestamp = time();
$query = "UPDATE phpb_transactions SET status='2', timestamp=? WHERE id=?";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $timestamp, $transactionid);
$stmt->execute();

// Check if transaction status was updated
if (!$stmt->affected_rows) {
    // Redirect to error page if transaction status was not updated
    header('Location: error.php');
    exit;
}

?>
<h2>The transaction was DENIED.</h2>
<a href="usercp.php">User CP</a>

<?php
} else {
    echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
    session_destroy();
}
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>