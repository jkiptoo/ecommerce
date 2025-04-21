<?php
session_start();
require('header.php');
require('db.php');

echo "<h1>Product Catalog</h1>";

// Retrieve all products
$prodsql = "SELECT * FROM products";
$prodres = mysqli_query($db, $prodsql);

echo "<ul>";
while($prodrow = mysqli_fetch_assoc($prodres)) {
    echo "<li><a href='" . htmlspecialchars($config_basedir) . "addtobasket.php?id=" . htmlspecialchars($prodrow['id']) . "'>" . 
         htmlspecialchars($prodrow['name']) . "</a></li>";
}
echo "</ul>";

mysqli_free_result($prodres);
require("footer.php");
?>