<?php
session_start();
$login = $_SESSION['login'];
$password = $_SESSION['password'];
require "config.php";
// Create a database connection using a secure connection string
$conn = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Account Information
$stmt = $conn->prepare("SELECT * FROM phpb_accounts WHERE login=?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();
$account_id = $account['id'];

// Get Other information
$stmt = $conn->prepare("SELECT * FROM phpb_other");
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

// Define a function to perform the transaction
function performTransaction($conn, $account_id, $requester, $acceptor, $direction, $amount, $comment) {
    // Check if the transaction is valid
    if (!$amount || $amount <= 0) {
        throw new Exception("Invalid transaction amount");
    }

    // Get the current balances of the accounts
    $stmt = $conn->prepare("SELECT balance FROM phpb_accounts WHERE id=?");
    $stmt->bind_param("i", $requester);
    $stmt->execute();
    $result = $stmt->get_result();
    $requester_balance = $result->fetch_assoc()['balance'];

    $stmt = $conn->prepare("SELECT balance FROM phpb_accounts WHERE id=?");
    $stmt->bind_param("i", $acceptor);
    $stmt->execute();
    $result = $stmt->get_result();
    $acceptor_balance = $result->fetch_assoc()['balance'];

    // Check if the requester has sufficient balance
    if ($requester_balance < $amount) {
        throw new Exception("Insufficient balance");
    }

    // Perform the transaction
    $stmt = $conn->prepare("UPDATE phpb_accounts SET balance=balance-? WHERE id=?");
    $stmt->bind_param("ii", $amount, $requester);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE phpb_accounts SET balance=balance+? WHERE id=?");
    $stmt->bind_param("ii", $amount, $acceptor);
    $stmt->execute();

    // Record the transaction
    $stmt = $conn->prepare("INSERT INTO phpb_transactions (requester, acceptor, direction, amount, comment, status) VALUES (?, ?, ?, ?, ?, '1')");
    $stmt->bind_param("iiiss", $requester, $acceptor, $direction, $amount, $comment);
    $stmt->execute();
}

// Process the transaction
if (isset($_POST['requester'], $_POST['acceptor'], $_POST['direction'], $_POST['amount'], $_POST['comment'])) {
    $requester = filter_input(INPUT_POST, 'requester', FILTER_SANITIZE_NUMBER_INT);
    $acceptor = filter_input(INPUT_POST, 'acceptor', FILTER_SANITIZE_NUMBER_INT);
    $direction = filter_input(INPUT_POST, 'direction', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        performTransaction($conn, $account_id, $requester, $acceptor, $direction, $amount, $comment);
        echo "<h2>Transaction successful!</h2>";
    } catch (Exception $e) {
        echo "<h2>Error: " . $e->getMessage() . "</h2>";
    }
} else {
    echo "<h3>ERROR: Invalid transaction request</h3>";
}

// Close the database connection
$conn->close();
?>