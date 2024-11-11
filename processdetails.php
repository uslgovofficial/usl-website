<?php
session_start();
$login = $_SESSION['login'];
$password = $_SESSION['password'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - Processing account details update</TITLE>
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

// Get Account Information
$query = "SELECT * FROM phpb_accounts WHERE login=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();
$accountid = $account['id'];

// Get Other information
$query = "SELECT * FROM phpb_other";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$other = $result->fetch_assoc();

if (isset($_POST['name'], $_POST['email'], $_POST['description'], $_POST['password1'], $_POST['password2'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password1 = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (strlen($description) > 100) {
        die('<h3>ERROR: The description is longer than 100 characters. Please limit its length to 100 at max.</h3><a href="details.php">Edit account details</a>');
    }

    if (strlen($name) > 30) {
        die('<h3>ERROR: The account name is longer than 30 characters. Please limit its length to 30 at max.</h3><a href="details.php">Edit account details</a>');
    }

    if (!$name) {
        die("<h3>ERROR: The account name is missing.</h3><a href='details.php'>Edit account details</a>");
    }
    if (!$email) {
        die("<h3>ERROR: The email is missing.</h3><a href='details.php'>Edit account details</a>");
    }

    if ($password1) {
        if ($password1 != $password2) {
            die('<h3>ERROR: The passwords you entered are different. All other changes have been stored.</h3><a href="details.php">Edit account details</a>');
        }
        $new_password = $password1;
    }

    // Update account information
    $query = "UPDATE phpb_accounts SET name=?, email=?, description=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $description, $accountid);
    $stmt->execute();

    if (isset($new_password)) {
        $query = "UPDATE phpb_accounts SET password=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_password, $accountid);
        $stmt->execute();
        echo 'Password updated. You will have to log out and log in again with your new password, else you will probably receive a "wrong password" error.<br>';
    }

    ?>
    <h2>Details changed.</h2>
    <a href="usercp.php">User CP</a>

<?php
} else {
    echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
    session_destroy();
}
?>
<BODY>
<table height="100%" width="100%" class="centertable"><tr><td class="centertable" height="100%" width="100%" valign="middle" align="center">
<table cellpadding="15"><tr><td align="center">
<img src="<?php echo $other['topimg']; ?>">
<?php
echo "<h1>" . $other['nation'] . " - PHPBank: Processing account details update</h1>";
?>
</td></tr></table>
</td></tr></table>
</BODY>
</HTML>