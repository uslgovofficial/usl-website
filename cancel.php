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

$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get account info
$query = "SELECT * FROM phpb_accounts where login=? AND password=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $login, $password);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

// Check if account exists
if (!$account) {
    // Redirect to login page if account does not exist
    header('Location: login.php');
    exit;
}

// Sanitize and validate account info
$account['login'] = filter_var($account['login'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$account['password'] = filter_var($account['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Get other information
$query = "SELECT * FROM phpb_other";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

// Sanitize and validate other information
$other['topimg'] = filter_var($other['topimg'], FILTER_SANITIZE_URL);
$other['nation'] = filter_var($other['nation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if password is correct
if ($password != $account['password']) {
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
    exit;
}

// Get transaction ID
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

// Get transaction info
$query = "SELECT * FROM phpb_transactions WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $transactionid);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

// Check if transaction exists
if (!$transaction) {
    // Redirect to error page if transaction does not exist
    header('Location: error.php');
    exit;
}

// Sanitize and validate transaction info
$transaction['id'] = filter_var($transaction['id'], FILTER_VALIDATE_INT);
$transaction['status'] = filter_var($transaction['status'], FILTER_VALIDATE_INT);

// Check if transaction is pending
if ($transaction['status'] != 0) {
    die ("<h3>ERROR: This transaction is no longer pending. It may have been accepted or denied in the meantime.</h3><a href='usercp.php'>User CP</a>");
}

// Delete transaction
$query = "DELETE FROM phpb_transactions WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $transactionid);
$stmt->execute();

// Check if transaction was deleted
if (!$stmt->affected_rows) {
    die ("<h3>ERROR: The transaction failed to be deleted! Please try again later.</h3><a href='usercp.php'>User CP</a>");
}

echo "<h2>The transaction has been CANCELLED.</h2>";
echo "<a href='usercp.php'>User CP</a>";

?>