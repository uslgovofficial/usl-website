<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML>
<HEAD>
<TITLE>PHPBank - Installation</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<style>

<!-- 
body { background-color: #FFFFFF; color: #000000; font-size: 11px; font-family: "Tahoma"; }

table { background-color: #000000; }

.centertable { background-color: #FFFFFF; }

td { background-color: #FFFFFF; font-size: 11px; font-family: "Tahoma"; }

i { font-size: 12px; font-style : italic; font-family: "Tahoma"; }

a { font-weight:bold; text-decoration: none; color: #888888; }

a:hover { font-weight:bold; text-decoration: none; color: #BBBBBB; }

h1 { color: #444444; font-size: 15px; font-weight:bold; }

h2 { color: #888888; font-size: 13px; font-weight:bold; }

h3 { color: #FF0000; font-size: 11px; font-weight:bold; }

--> 
</style>
</HEAD>
<BODY>
<table height=100% width=100% class=centertable><tr><td class=centertable height=100% width=100% valign=middle align=center>
<table cellpadding=15><tr><td align=center colspan=2>
<img src="topimg.png">
<h1>PHPBank Installation - Installing</h1>
<?php

// Get the posted database credentials
$dathost = $_POST['dathost'];
$datusr = $_POST['datusr'];
$datpass = $_POST['datpass'];
$datname = $_POST['datname'];

$login = $_POST['login'];
$password = $_POST['password'];
$name = $_POST['name'];
$description = $_POST['description'];
$balance = $_POST['balance'];
$email = $_POST['email'];

$intromessage = $_POST['intromessage'];
$nation = $_POST['nation'];
$currencysymbol = $_POST['currencysymbol'];
$apassword = $_POST['apassword'];
// Check if any of the fields are blank
if (empty($dathost) || empty($datusr) || empty($datpass) || empty($datname)) {
    die("Error: Please fill in all database configuration fields.");
}

// Create a config.json file with the database configuration
$config = array(
    "dathost" => $dathost,
    "datusr" => $datusr,
    "datpass" => $datpass,
    "datname" => $datname
);

$json = json_encode($config);
file_put_contents("config.json", $json);

// Include the config.json file
$config = json_decode(file_get_contents("config.json"), true);

// Create a connection to the database
$conn = mysqli_connect($config["dathost"], $config["datusr"], $config["datpass"], $config["datname"]);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if table exists
if (mysqli_query($conn, "SHOW TABLES LIKE 'phpb_accounts'")->num_rows == 0) {
    $query = "CREATE TABLE phpb_accounts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        login VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(30) NOT NULL,
        description VARCHAR(100) NOT NULL,
        balance DECIMAL(10, 2) NOT NULL,
        active INT NOT NULL DEFAULT 0,
        email VARCHAR(255) NOT NULL
    )";

    if (!mysqli_query($conn, $query)) {
        die ('<h3>ERROR: Could not create the account table. ' . mysqli_error($conn) . '</h3>');
    }
    echo "..Account table created.<br>";
} else {
    echo "..Account table already exists.<br>";
}

// Create structure
$query = "CREATE TABLE phpb_layout (
   bordercolor VARCHAR(7) NOT NULL,
   bgcolor VARCHAR(7) NOT NULL,
   insidecolor VARCHAR(7) NOT NULL,
   h1color VARCHAR(7) NOT NULL,
   h1size INT NOT NULL,
   h2color VARCHAR(7) NOT NULL,
   h2size INT NOT NULL,
   h3color VARCHAR(7) NOT NULL,
   h3size INT NOT NULL,
   acolor VARCHAR(7) NOT NULL,
   ahovercolor VARCHAR(7) NOT NULL,
   size INT NOT NULL,
   textcolor VARCHAR(7) NOT NULL,
   font VARCHAR(20) NOT NULL
)";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not create the layout table. ' . mysqli_error($conn) . '</h3>');
}
echo "..Layout table created.<br>";

$query = "CREATE TABLE phpb_other (
   accounts INT NOT NULL DEFAULT 0,
   transactions INT NOT NULL DEFAULT 0,
   latesttransaction TEXT NOT NULL,
   intromessage TEXT NOT NULL,
   nation TEXT NOT NULL,
   currencysymbol TEXT NOT NULL,
   topimg TEXT NOT NULL,
   apassword TEXT NOT NULL
)";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not create the information table. ' . mysqli_error($conn) . '</h3>');
}
echo "..Information table created.<br>";

$query = "CREATE TABLE phpb_transactions (
   id INT PRIMARY KEY AUTO_INCREMENT,
   requester INT NOT NULL DEFAULT 0,
   acceptor INT NOT NULL DEFAULT 0,
   direction INT NOT NULL DEFAULT 0,
   amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
   comment TEXT NOT NULL,
   status INT NOT NULL DEFAULT 0,
   timestamp TEXT NOT NULL
)";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not create the transaction table. ' . mysqli_error($conn) . '</h3>');
}
echo "..Transaction table created.<br><br>";

$query = "INSERT INTO phpb_other VALUES (1, 0, '', '$intromessage', '$nation', '$currencysymbol', 'topimg.png', '$apassword')";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not insert bank data! ' . mysqli_error($conn) . '</h3>');
}
echo "..Bank data inserted.<br>";

$query = "INSERT INTO phpb_layout VALUES ('#000000', '#FFFFFF', '#000000', '#444444', 15, '#888888', 13, '#FF0000', 11, '#888888', '#BBBBBB', 11, '#000000', 'Tahoma')";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not insert layout data! ' . mysqli_error($conn) . '</h3>');
}
echo "..Layout data inserted.<br>";

$query = "INSERT INTO phpb_accounts (login, password, name, description, balance, email) VALUES ('$login', '$password', '$name', '$description', '$balance', '$email')";

if (!mysqli_query($conn, $query))
{
   die ('<h3>ERROR: Could not insert account data! ' . mysqli_error($conn) . '</h3>');
}
echo "..Account data inserted.<br><br>";

echo "<h3>Installation complete!</h3>";
echo "<p>We strongly recommend to delete install.php and doinstall.php files to prevent unauthorized access to your installation.</p>";
?>
</td></tr></table></td></tr></table></BODY></HTML>