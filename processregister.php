<?php
require "config.php";

// Connect to database using prepared statements
$mysqli = new mysqli($dathost, $datusr, $datpass, $datname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get posted data and sanitize it
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

// Check for missing fields
if (!$name || !$login || !$password || !$email) {
    die("<h3>ERROR: One or more required fields are missing. Please hit the back button of your browser and try again.</h3>");
}

// Check password match
if ($_POST['password1'] != $_POST['password2']) {
    die("<h3>ERROR: The passwords you typed are different. Please hit the back button of your browser.</h3>");
}

// Check login ID characters
if (!preg_match('/^[a-z]+$/', $login)) {
    die ("<h3>ERROR: Your login ID can contain only letters.</h3>");
}

// Check for duplicate login ID using prepared statement
$stmt = $mysqli->prepare("SELECT * FROM phpb_accounts WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die ("<h3>ERROR: Your login ID is already taken. Please pick another one.</h3>");
}

// Check description length
if (strlen($description) > 100) {
    die ('<h3>ERROR: The description is longer than 100 characters. Please limit its length to 100 at max.</h3>');
}

// Check name length
if (strlen($name) > 30) {
    die ('<h3>ERROR: The account name is longer than 30 characters. Please limit its length to 30 at max.</h3>');
}

// Check login ID length
if (strlen($login) > 20) {
    die ('<h3>ERROR: The login id is longer than 20 characters. Please limit its length to 20 at max.</h3>');
}

// Hash the password using bcrypt
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Insert account information using prepared statement
$stmt = $mysqli->prepare("INSERT INTO phpb_accounts (login, password, name, description, balance, active, email) VALUES (?, ?, ?, ?, '0', '1', ?)");
$stmt->bind_param("ssssss", $login, $password_hash, $name, $description, $email);
$stmt->execute();

if (!$stmt->affected_rows) {
    die ("<h3>ERROR: PHPBank failed to store your account information in the database. Please try again later.</h3>");
}

echo "Your Registration was successful! You will receive an email with further details.<br><br>";
echo "<a href=\"index.php\">Index</a>";
?>