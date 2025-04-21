<?php
session_start();
require("config.php");

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: " . $config_basedir);
exit;
?>