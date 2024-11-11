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

require "config.php";

// Create connection
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get account info
$query = "SELECT * FROM phpb_accounts WHERE login=? AND password=?";
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

// Check if account is active
if (!$account['active']) {
    echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
    session_destroy();
    exit;
}

// Check if password is correct
if ($password != $account['password']) {
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
    exit;
}

// Get denied transactions
$query = "SELECT * FROM phpb_transactions WHERE acceptor=? AND status='2' ORDER BY timestamp DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account['id']);
$stmt->execute();
$result = $stmt->get_result();

// Display denied transactions
while ($transactions = $result->fetch_assoc()) {
    $query = "SELECT * FROM phpb_accounts WHERE id=?";
    $stmt2 = $conn->prepare($query);
    $stmt2->bind_param("i", $transactions['requester']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $requesterarray = $result2->fetch_assoc();

    echo "<tr><td>" . htmlspecialchars($requesterarray['name']) . "</td>";
    if ($transactions['direction']) {
        $direction = "Incoming";
    } else {
        $direction = "Outgoing";
    }
    echo "<td>" . htmlspecialchars($direction) . "</td>";
    echo "<td>" . htmlspecialchars($currencysymbol) . " " . $transactions['amount'] . "</td>";
    echo "<td>" . htmlspecialchars($transactions['comment']) . "</td>";
    $datetime = date("D j F, g:i A", $transactions['timestamp']);
    echo "<td>" . htmlspecialchars($datetime) . "</td></tr>";
}

// Get denied transactions by others
$query = "SELECT * FROM phpb_transactions WHERE requester=? AND status='2' ORDER BY timestamp DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account['id']);
$stmt->execute();
$result = $stmt->get_result();

// Display denied transactions by others
while ($transactions = $result->fetch_assoc()) {
    $query = "SELECT * FROM phpb_accounts WHERE id=?";
    $stmt2 = $conn->prepare($query);
    $stmt2->bind_param("i", $transactions['acceptor']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $acceptorarray = $result2->fetch_assoc();

    echo "<tr><td>" . htmlspecialchars($acceptorarray['name']) . "</td>";
    if ($transactions['direction']) {
        $direction = "Outgoing";
    } else {
        $direction = "Incoming";
    }
    echo "<td>" . htmlspecialchars($direction) . "</td>";
    echo "<td>" . htmlspecialchars($currencysymbol) . " " . $transactions['amount'] . "</td>";
    echo "<td>" . htmlspecialchars($transactions['comment']) . "</td>";
    $datetime = date("D j F, g:i A", $transactions['timestamp']);
    echo "<td>" . htmlspecialchars($datetime) . "</td></tr>";
}

?>