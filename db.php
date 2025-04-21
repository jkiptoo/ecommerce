<?php
require("config.php");

// Create database connection
$db = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbdatabase);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($db, "utf8");
?>