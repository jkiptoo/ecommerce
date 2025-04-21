<h1>Browse Product Categories</h1>
<ul>
<?php
require("db.php");

// Retrieve all product categories
$catsql = "SELECT * FROM categories";
$catres = mysqli_query($db, $catsql);

while($catrow = mysqli_fetch_assoc($catres)) {
    echo "<li><a href='" . htmlspecialchars($config_basedir) . "products.php?id=" . htmlspecialchars($catrow['id']) . "'>" . htmlspecialchars($catrow['name']) . "</a></li>";
}

mysqli_free_result($catres);
?>
</ul>