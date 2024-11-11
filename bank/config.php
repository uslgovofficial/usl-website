<?php
// Load the config.json file
$config = json_decode(file_get_contents("config.json"), true);

// Define constants for compatibility
define('DATPASS', $config["datpass"]);
define('DATHOST', $config["dathost"]);
define('DATUSR', $config["datusr"]);
define('DATNAME', $config["datname"]);

// Assign constants to variables for compatibility
$datpass = DATPASS;
$dathost = DATHOST;
$datusr = DATUSR;
$datname = DATNAME;
?>