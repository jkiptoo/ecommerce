<?php
session_start();
require("config.php");
require("functions.php");
require("db.php");

// Check if administrator is logged in
if(!isset($_SESSION['SESS_ADMINLOGGEDIN'])) {
    header("Location: " . $config_basedir);
    exit;
}

// Validate the id
$validid = pf_validate_number($_GET['id'] ?? 0, "redirect", $config_basedir . "adminorders.php");

require("header.php");

echo "<h1>Order Details</h1>";
echo "<a href='adminhome.php'><--- Return to the administrative panel</a>";
echo "<p>";
echo "<a href='adminorders.php'><-- Return to the main orders screen</a>";

// Retrieve order details using prepared statement
$ordsql = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($db, $ordsql);
mysqli_stmt_bind_param($stmt, "i", $validid);
mysqli_stmt_execute($stmt);
$ordres = mysqli_stmt_get_result($stmt);
$ordrow = mysqli_fetch_assoc($ordres);
mysqli_stmt_close($stmt);

echo "<table cellpadding=10>";
echo "<tr><td><strong>Order Number</strong></td><td>" . htmlspecialchars($ordrow['id']) . "</td>";
echo "<tr><td><strong>Date of order</strong></td><td>" . date('D jS F Y g.iA', strtotime($ordrow['date'])) . "</td>";
echo "<tr><td><strong>Payment Type</strong></td><td>";

if($ordrow['payment_type'] == 1) {
    echo "PayPal";
}
else {
    echo "Cheque";
}

echo "</td>";
echo "</table>";

// Get address details
if($ordrow['delivery_add_id'] == 0) {
    $addsql = "SELECT * FROM customers WHERE id = ?";
    $stmt = mysqli_prepare($db, $addsql);
    mysqli_stmt_bind_param($stmt, "i", $ordrow['customer_id']);
}
else {
    $addsql = "SELECT * FROM delivery_addresses WHERE id = ?";
    $stmt = mysqli_prepare($db, $addsql);
    mysqli_stmt_bind_param($stmt, "i", $ordrow['delivery_add_id']);
}

mysqli_stmt_execute($stmt);
$addres = mysqli_stmt_get_result($stmt);
$addrow = mysqli_fetch_assoc($addres);
mysqli_stmt_close($stmt);

echo "<table cellpadding=10>";
echo "<tr>";
echo "<td><strong>Address</strong></td>";
echo "<td>" . htmlspecialchars($addrow['forename']) . " " . htmlspecialchars($addrow['surname']) . "<br>";
echo htmlspecialchars($addrow['add1']) . "<br>";
echo htmlspecialchars($addrow['add2']) . "<br>";
echo htmlspecialchars($addrow['add3']) . "<br>";
echo htmlspecialchars($addrow['postcode']) . "<br>";

echo "<br>";

if($ordrow['delivery_add_id'] == 0) {
    echo "<i>Address from member account</i>";
}
else {
    echo "<i>Different delivery address</i>";
}

echo "</td></tr>";
echo "<tr><td><strong>Phone</strong></td><td>" . htmlspecialchars($addrow['phone']) . "</td></tr>";
echo "<tr><td><strong>Email</strong></td><td><a href='mailto:" . htmlspecialchars($addrow['email']) . "'>" . htmlspecialchars($addrow['email']) . "</a></td></tr>";
echo "</table>";

// Get order items
$itemssql = "SELECT products.*, orderitems.*, orderitems.id AS itemid 
             FROM products, orderitems 
             WHERE orderitems.product_id = products.id AND order_id = ?";
$stmt = mysqli_prepare($db, $itemssql);
mysqli_stmt_bind_param($stmt, "i", $validid);
mysqli_stmt_execute($stmt);
$itemsres = mysqli_stmt_get_result($stmt);
$itemnumrows = mysqli_num_rows($itemsres);

echo "<h1>Products Purchased</h1>";
echo "<table cellpadding=10>";
echo "<th></th>";
echo "<th>Product</th>";
echo "<th>Quantity</th>";
echo "<th>Price</th>";
echo "<th>Total</th>";

$total = 0;
while($itemsrow = mysqli_fetch_assoc($itemsres)) {    
    $quantitytotal = $itemsrow['price'] * $itemsrow['quantity'];
    $total += $quantitytotal;
    
    echo "<tr>";
    if(empty($itemsrow['image'])) {
        echo "<td><img src='./images/No Image.jpg' width='50' alt='" . htmlspecialchars($itemsrow['name']) . "'></td>";
    }
    else {
        echo "<td><img src='./images/" . htmlspecialchars($itemsrow['image']) . "' width='50' alt='" . htmlspecialchars($itemsrow['name']) . "'></td>";
    }

    echo "<td>" . htmlspecialchars($itemsrow['name']) . "</td>";
    echo "<td>" . htmlspecialchars($itemsrow['quantity']) . "</td>";
    echo "<td><strong>&pound;" . sprintf('%.2f', $itemsrow['price']) . "</strong></td>";
    echo "<td><strong>&pound;" . sprintf('%.2f', $quantitytotal) . "</strong></td>";
    echo "</tr>";
}                        

echo "<tr>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td>TOTAL</td>";
echo "<td><strong>&pound;" . sprintf('%.2f', $total) . "</strong></td>";
echo "</tr>";

echo "</table>";
mysqli_stmt_close($stmt);

require("footer.php");
?>