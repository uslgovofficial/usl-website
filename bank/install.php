<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPBank - Installation</title>
    <style>
        body {
            background-color: #FFFFFF;
            color: #000000;
            font-size: 11px;
            font-family: "Tahoma";
        }

        table {
            background-color: #000000;
        }

        .centertable {
            background-color: #FFFFFF;
        }

        td {
            background-color: #FFFFFF;
            font-size: 11px;
            font-family: "Tahoma";
        }

        i {
            font-size: 12px;
            font-style: italic;
            font-family: "Tahoma";
        }

        a {
            font-weight: bold;
            text-decoration: none;
            color: #888888;
        }

        a:hover {
            font-weight: bold;
            text-decoration: none;
            color: #BBBBBB;
        }

        h1 {
            color: #444444;
            font-size: 15px;
            font-weight: bold;
        }

        h2 {
            color: #888888;
            font-size: 13px;
            font-weight: bold;
        }

        h3 {
            color: #FF0000;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="centertable" height="100%" width="100%">
        <tr>
            <td class="centertable" height="100%" width="100%" valign="middle" align="center">
                <table cellpadding="15">
                    <tr>
                        <td align="center" colspan="2">
                            <img src="topimg.png" alt="Top Image">
                            <h1>PHPBank Installation</h1>
                            <p>Welcome to the PHPBank installation. Thank you for choosing us!</p>
                            <p>(not that you have much choice, anyways :-P)</p>
                            <h2>Before installing...</h2>
                            <p>...make sure that you have uploaded everything correctly.</p>
                            <p>You also have to edit the config.php file, for example in notepad. Make sure that all this has been done.</p>
                            <h2>Requirements</h2>
                            <p>PHPBank requires:</p>
                            <ul>
                                <li>PHP8; It was programmed in PHP8.3.6 but it will probably work with older versions too.</li>
                                <li>MySQL; It was programmed in MySQL 8 which is a fairly old version.</li>
                                <li>Note: Before installing, make sure that PHP supports the <b>mail</b>(); function!</li>
                            </ul>
                            <h2>Bank data</h2>
                            <form name="data" action="doinstall.php" method="post">
                                <table width="100%" cellspacing="0">
                                    <tr>
                                        <td width="33%">
                                            <label for="nation">Full nation name:</label>
                                            <br>
                                            <input type="text" name="nation" id="nation">
                                        </td>
                                        <td width="33%">
                                            <label for="currencysymbol">Currency symbol:</label>
                                            <br>
                                            <input type="text" name="currencysymbol" id="currencysymbol">
                                        </td>
                                        <td width="33%">
                                            <label for="apassword">Administration password:</label>
                                            <br>
                                            <input type="password" name="apassword" id="apassword">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="center">
                                            <br>
                                            <label for="intromessage">Message for the index page (you may use HTML):</label>
                                            <br>
                                            <textarea name="intromessage" id="intromessage" cols="40" rows="4"></textarea>
                                            <br>
                                            <br>
                                        </td>
                                    </tr>
                                </table>
                                <h2>Government bank account data</h2>
                                <p>The government bank account functions just like any other account,</p>
                                <p>except that it has a lot of money in it from start :)</p>
                                <p>This starting amount is very important as it can not be changed.</p>
                                <br>
                                <table width="100%" cellspacing="0">
                                    <tr>
                                        <td width="33%">
                                            <label for="login">Log in:</label>
                                            <br>
                                            <input type="text" name="login" id="login">
                                        </td>
                                        <td width="33%">
                                            <label for="password">Password:</label>
                                            <br>
                                            <input type="password" name="password" id="password">
                                        </td>
                                        <td width="33%">
                                            <label for="name">Account name:</label>
                                            <br>
                                            <input type="text" name="name" id="name">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="description">Description:</label>
                                            <br>
                                            <textarea name="description" id="description" cols="15" rows="3"></textarea>
                                        </td>
                                        <td>
                                            <label for="balance">Starting balance:</label>
                                            <br>
                                            <input type="number" name="balance" id="balance">
                                            <br>
                                            Consider this very carefully!
                                        </td>
                                        <td>
                                            <label for="email">Email:</label>
                                            <br>
                                            <input type="email" name="email" id="email">
                                        </td>
                                    </tr>
                                    <h2>Database settings</h2>
                                    <form name="data" action="doinstall.php" method="post">
                                        <table width="100%" cellspacing="0">
                                            <tr>
                                                <td width="33%">
                                                    <label for="dathost">Database Host:</label>
                                                    <br>
                                                    <input type="text" name="dathost" id="dathost">
                                                </td>
                                                <td width="33%">
                                                    <label for="datusr">Database Username:</label>
                                                    <br>
                                                    <input type="text" name="datusr" id="datusr">
                                                </td>
                                                <td width="33%">
                                                    <label for="datpass">Database Password:</label>
                                                    <br>
                                                    <input type="password" name="datpass" id="datpass">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <label for="datname">Database Name:</label>
                                                    <br>
                                                    <input type="text" name="datname" id="datname">
                                                </td>
                                            </tr>
                                        </table>    
                                </table>
                                <br>
                                <input type="submit" value="Install PHPBank">
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>