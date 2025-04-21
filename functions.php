<?php
// Function to validate numbers
function pf_validate_number($value, $function, $redirect) {
    if(isset($value)) {
        if(!is_numeric($value)) {
            header("Location: " . $redirect);
            exit;
        }
        return (int)$value;
    }
    else {
        if($function == 'redirect') {
            header("Location: " . $redirect);
            exit;
        }
        return 0;
    }
}

// Function to show cart
function showcart() {
    global $config_basedir, $db;
    
    $total = 0;
    
    // Check if order exists
    if(isset($_SESSION['SESS_ORDERNUM'])) {
        if(isset($_SESSION['SESS_LOGGEDIN'])) {
            // For logged in users
            $custsql = "SELECT id, status FROM orders WHERE customer_id = ? AND status < 2";
            $stmt = mysqli_prepare($db, $custsql);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_USERID']);
            mysqli_stmt_execute($stmt);
            $custres = mysqli_stmt_get_result($stmt);
            $custrow = mysqli_fetch_assoc($custres);
            mysqli_stmt_close($stmt);
        }
        else {
            // For guests
            $custsql = "SELECT id, status FROM orders WHERE session = ? AND status < 2";
            $stmt = mysqli_prepare($db, $custsql);
            $sess_id = session_id();
            mysqli_stmt_bind_param($stmt, "s", $sess_id);
            mysqli_stmt_execute($stmt);
            $custres = mysqli_stmt_get_result($stmt);
            $custrow = mysqli_fetch_assoc($custres);
            mysqli_stmt_close($stmt);
        }
        
        if($custrow) {
            $itemssql = "SELECT products.*, orderitems.*, orderitems.id AS itemid 
                        FROM products, orderitems 
                        WHERE orderitems.product_id = products.id AND order_id = ?";
            $stmt = mysqli_prepare($db, $itemssql);
            mysqli_stmt_bind_param($stmt, "i", $custrow['id']);
            mysqli_stmt_execute($stmt);
            $itemsres = mysqli_stmt_get_result($stmt);
            $itemnumrows = mysqli_num_rows($itemsres);
        }
        else {
            $itemnumrows = 0;
        }
    }
    else {
        $itemnumrows = 0;
    }

    if($itemnumrows == 0) {
        echo "<p>You have not added anything to your shopping cart yet.</p>";
        return;
    }

    // Display cart contents
    echo "<table cellpadding='10'>";
    echo "<tr>";
        echo "<td></td>";
        echo "<td><strong>Item</strong></td>";
        echo "<td><strong>Quantity</strong></td>";
        echo "<td><strong>Unit Price</strong></td>";
        echo "<td><strong>Total Price</strong></td>";
        echo "<td></td>";
    echo "</tr>";
        
    while($itemsrow = mysqli_fetch_assoc($itemsres)) {
        $quantitytotal = $itemsrow['price'] * $itemsrow['quantity'];
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
        echo "<td>[<a href='" . htmlspecialchars($config_basedir) . "delete.php?id=" . htmlspecialchars($itemsrow['itemid']) . "'>REMOVE</a>]</td>";
        echo "</tr>";
        
        $total += $quantitytotal;
    }

    echo "<tr>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td>TOTAL</td>";
        echo "<td><strong>&pound;" . sprintf('%.2f', $total) . "</strong></td>";
        echo "<td></td>";
    echo "</tr>";
    echo "</table>";
    
    mysqli_stmt_close($stmt);
}
?>