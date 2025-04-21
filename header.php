<?php
session_start();

// Check for session change
if(isset($_SESSION['SESS_CHANGEID']) == TRUE) {
    $_SESSION = array();
    session_regenerate_id(true);
    unset($_SESSION['SESS_CHANGEID']);
}

require("config.php");
require("db.php");

// Check for HTTPS (recommended for production)
// if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
//     $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header("Location: $redirect");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($config_sitename); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="icon" type="image/png" href="images/favicon.png">  
</head>
<body>
    <div id="header">
        <h1><?php echo htmlspecialchars($config_sitename); ?></h1>
    </div>
    <div id="menu">
        <a href="<?php echo htmlspecialchars($config_basedir); ?>">HOME</a> -
        <a href="<?php echo htmlspecialchars($config_basedir); ?>register.php">REGISTER</a> -
        <a href="<?php echo htmlspecialchars($config_basedir); ?>login.php">LOG IN</a> -
        <a href="<?php echo htmlspecialchars($config_basedir); ?>catalog.php">OUR PRODUCTS</a> -
        <a href="<?php echo htmlspecialchars($config_basedir); ?>showcart.php">VIEW CART/CHECKOUT</a>
    </div>
    <div id="container">
        <div id="bar">
            <?php require("bar.php"); ?>
            <hr>
            <?php
            if(isset($_SESSION['SESS_LOGGEDIN']) && $_SESSION['SESS_LOGGEDIN'] == 1) {
                echo "You are logged in as <strong>" . htmlspecialchars($_SESSION['SESS_USERNAME']) . "</strong> ";
                echo "[<a href='" . htmlspecialchars($config_basedir) . "logout.php'>SIGN OUT</a>]";
            }
            else {
                echo "<a href='" . htmlspecialchars($config_basedir) . "login.php'>SIGN IN</a>";
            }
            ?>
            <br><br>
            <?php
            if(isset($_SESSION['SESS_ADMINLOGGEDIN']) && $_SESSION['SESS_ADMINLOGGEDIN'] == 1) {
                echo "<a href='" . htmlspecialchars($config_basedir) . "adminhome.php'>ADMINISTRATOR</a>";
            }
            ?>
        </div>
        <div id="main">