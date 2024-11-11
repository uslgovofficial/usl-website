<?php
session_start();

if (!isset($_SESSION['password'])) {
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Sanitize and validate input data
    if (empty($login) || empty($password)) {
        echo "Error: Invalid input data";
        exit;
    }

    // Connect to database using prepared statements
    $mysqli = new mysqli($dathost, $datusr, $datpass, $datname);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Retrieve hashed password from database
    $stmt = $mysqli->prepare("SELECT password FROM phpb_accounts WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify hashed password
        $salt = substr($hashed_password, 0, 29);
        $hash = crypt($password, $salt);
        if ($hash == $hashed_password) {
            // Password is valid, store sanitized and validated input data in session variables
            $_SESSION['login'] = $login;
            $_SESSION['password'] = $password;
            echo "Login successful!";
        } else {
            echo "Error: Invalid password";
        }
    } else {
        echo "Error: Invalid login";
    }
} else {
    $login = $_SESSION['login'];
    $password = $_SESSION['password'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>PHPBank - UserCP</TITLE>
<META NAME="GENERATOR" CONTENT="MAX's HTML Beauty++ ME">
<link rel=stylesheet type=text/css href="style.php">
</HEAD>
<?php
require "config.php";

$db = new mysqli($dathost, $datusr, $datpass, $datname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

//get account info
$query = "SELECT * FROM phpb_accounts where login='$login'";
$result = $db->query($query);
$account = $result->fetch_array();
$accountid = $account['id'];

// Check if the password is okay and activity
if ($password==$account['password'])
{
    if ($account['active'])
    {
        echo "<h2>".$account['name']." - ".(isset($account['currencysymbol']) ? $account['currencysymbol'] : '')." ".$account['balance']."</h2>";
        ?>
        <table width=100% cellspacing=1>
            <tr>
                <td align=center>
                    <h2>Pending transactions</h2>
                    <table width=100% cellspacing=1>
                        <tr>
                            <td>Account name</td>
                            <td>Direction</td>
                            <td>Amount</td>
                            <td>Comment</td>
                            <td>Action</td>
                        </tr>
                        <?php
                        //First, get transactions that I requested
                        $query="SELECT * FROM `phpb_transactions` WHERE `requester`='$accountid'";
                        $result=$db->query($query);
                        while ($rqtransaction=$result->fetch_array())
                        {
                            if ($rqtransaction['status']==0)
                            {
                                //get other guy's name
                                $acid=$rqtransaction['acceptor'];
                                $query="SELECT * FROM `phpb_accounts` WHERE `id`='$acid'";
                                $result2=$db->query($query);
                                $acaccount=$result2->fetch_array();
                                if ($rqtransaction['direction'])
                                {$direction="Outgoing";}
                                else
                                {$direction="Incoming";}
                                echo "<tr><td>".$acaccount['name']."</td><td>".$direction."</td><td>".$account['currencysymbol']." ".$rqtransaction['amount']."</td><td>".$rqtransaction['comment']."</td><td>";
                                echo "<form style='display: inline;' action='cancel.php' method=post><input type=hidden name=transactionid value='".$rqtransaction['id']."'><input type=submit value='Cancel'></form>";
                                echo "</td></tr>";
                            }
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=center>
                    <h2>New transaction</h2>
                    <form name=newtransaction action="processnewtransaction.php" method=post>
                        <table width=100% cellspacing=0>
                            <tr>
                                <td width=33%>
                                    Who with:<br>
                                    <select name=who>
                                        <?php
                                        $query="SELECT * FROM `phpb_accounts`";
                                        $result=$db->query($query);
                                        while ($listaccount=$result->fetch_array())
                                        {
                                            if ($listaccount['id']!=$account['id'])
                                            {
                                                echo "<option value='".$listaccount['id']."'>".$listaccount['name']."</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td width=33%>
                                    Direction:<br>
                                    <input type=radio name=direction value="1" checked>Send money<br>
                                    <input type=radio name=direction value="0">Request money<br>
                                </td>
                                <td width=33%>
                                    Amount:<br>
                                    <input type=text name=amount><br>
                                    Enter only numbers
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    Comment:<br>
                                    <textarea name=comment rows=3 cols=20></textarea><br>Max. 40 characters
                                </td>
                                <td>
                                    <input type=submit value="Create transaction">
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
            <tr>
                <td align=center>
                    <?php
                    echo $account['description'];
                    ?>
                </td>
            </tr>
            <tr>
                <td align=center>
                    <a href="accepted.php">Accepted transactions</a> - <a href="denied.php">Denied transactions</a> - <a href="details.php">Change account details</a> - <a href="logout.php">Log out</a>
                </td>
            </tr>
        </table>
    <?php
    }
    else
    {
        echo "<h3>ERROR: You are trying to view an account that is not activated yet.</h3>";
        session_destroy();
    }
}
else
{
    echo "<h3>ERROR: Wrong Password. Please try again by hitting the back button of your browser.</h3>";
    session_destroy();
}
?>
</BODY>
</HTML>