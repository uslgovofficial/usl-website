<?php
session_start();

// Check if session variables are set
if (!isset($_SESSION['apassword'])) {
    // Redirect to login page if session variables are not set
    header('Location: login.php');
    exit;
}

// Get session variables
$apassword = $_SESSION['apassword'];

// Sanitize and validate session variables
$apassword = filter_var($apassword, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if session variables are valid
if (empty($apassword)) {
    // Redirect to login page if session variables are invalid
    header('Location: login.php');
    exit;
}

require "config.php";

// Create connection
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get other information
$query = "SELECT * FROM phpb_other";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

// Sanitize and validate other information
$other['topimg'] = filter_var($other['topimg'], FILTER_VALIDATE_URL);
$other['nation'] = filter_var($other['nation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if password is correct
if ($apassword != $other['apassword']) {
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
    exit;
}

// Get account ID
if (!isset($_POST['accountid'])) {
    // Redirect to error page if account ID is not set
    header('Location: error.php');
    exit;
}

// Sanitize and validate account ID
$accountid = filter_var($_POST['accountid'], FILTER_VALIDATE_INT);

// Check if account ID is valid
if (!$accountid) {
    // Redirect to error page if account ID is invalid
    header('Location: error.php');
    exit;
}

// Get account info
$query = "SELECT * FROM phpb_accounts WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $accountid);
$stmt->execute();
$result = $stmt->get_result();
$accountinfo = $result->fetch_assoc();

// Check if account exists
if (!$accountinfo) {
    // Redirect to error page if account does not exist
    header('Location: error.php');
    exit;
}

// Delete account
$query = "DELETE FROM phpb_accounts WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $accountid);
$stmt->execute();

// Check if account was deleted
if (!$stmt->affected_rows) {
    die ("<h3>ERROR: The account couldn't be deleted. Please try again later.</h3>");
}

// Send email to account owner
$to = $accountinfo['email'];
$subject = "PHPBank: Account deleted";
$message = "Your account request at the PHPBank of " . $other['nation'] . " has been turned down.";
mail($to, $subject, $message);

echo "The account was deleted.<br><br>";
echo "<a href='admincp.php'>Admin CP</a>";

?>