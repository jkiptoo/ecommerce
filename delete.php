<?php
session_start();
require("config.php");
require("db.php");
require("functions.php");

// Validate ID
$validid = pf_validate_number($_GET['id'] ?? 0, "redirect", $config_basedir . "showcart.php");

// Check if order belongs to user
if(!isset($_SESSION['SESS_ORDERNUM'])) {
    header("Location: " . $config_basedir . "showcart.php");
    exit;
}

// Get item details using prepared statement
$itemsql = "SELECT * FROM orderitems WHERE id = ?";
$stmt = mysqli_prepare($db, $itemsql);
mysqli_stmt_bind_param($stmt, "i", $validid);
mysqli_stmt_execute($stmt);
$itemres = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($itemres) == 0) {
    header("Location: " . $config_basedir . "showcart.php");
    exit;
}

$itemrow = mysqli_fetch_assoc($itemres);
mysqli_stmt_close($stmt);

// Get product price
$prodsql = "SELECT price FROM products WHERE id = ?";
$stmt = mysqli_prepare($db, $prodsql);
mysqli_stmt_bind_param($stmt, "i", $itemrow['product_id']);
mysqli_stmt_execute($stmt);
$prodres = mysqli_stmt_get_result($stmt);
$prodrow = mysqli_fetch_assoc($prodres);
mysqli_stmt_close($stmt);

// Delete item
$delsql = "DELETE FROM orderitems WHERE id = ?";
$stmt = mysqli_prepare($db, $delsql);
mysqli_stmt_bind_param($stmt, "i", $validid);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Update order total
$totalprice = $prodrow['price'] * $itemrow['quantity'];
$updsql = "UPDATE orders SET total = total - ? WHERE id = ?";
$stmt = mysqli_prepare($db, $updsql);
mysqli_stmt_bind_param($stmt, "di", $totalprice, $_SESSION['SESS_ORDERNUM']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: " . $config_basedir . "showcart.php");
exit;
?>