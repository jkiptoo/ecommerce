<?php

session_start();


if(isset($_SESSION['SESS_ADMINLOGGEDIN']) == FALSE) {
	header("Location: " . $config_basedir . "adminlogin.php");
}

require("header.php");
	echo "<h1>Administrative Panel</h1>";
	echo "Welcome <strong>" . $_SESSION['SESS_ADMINUSERNAME'] . "</strong>";
?>
<table border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td><a href="adminorders.php">Access orders</a></td>
    <td>View orders</td>
  </tr>
  <tr>
    <td><a href="addcategory.php">Add Category</a></td>
    <td>Add a new product category</td>
  </tr>
  <tr>
    <td><a href="addproduct.php">Add Product</a></td>
    <td>Add a new product</td>
    <tr>
    <td><a href="adminupload.php">Upload images</a></td>
    <td>Upload product images</td>
  </tr>
  </tr>
  </table>

<?php
	require("footer.php");
?>