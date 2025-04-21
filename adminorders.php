<?php
session_start();

require("config.php");
require("db.php");
require("functions.php");

// Check if administrator is logged in     
if(!isset($_SESSION['SESS_ADMINLOGGEDIN'])) {
    header("Location: " . $config_basedir);
    exit;
}

// Check if 'func' GET parameter exists
if(isset($_GET['func'])) {
    if($_GET['func'] != "conf") {
        header("Location: " . $config_basedir);
        exit;
    }
    
    // Validate the id
    $validid = pf_validate_number($_GET['id'], "redirect", $config_basedir);
    
    // Update order status using prepared statement
    $funcsql = "UPDATE orders SET status = 10 WHERE id = ?";
    $stmt = mysqli_prepare($db, $funcsql);
    mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $config_basedir . "adminorders.php");
    exit;
}
else {
    require("header.php");
    echo "<h1>Outstanding orders</h1>";
    echo "<a href='adminhome.php'><--- Return to the administrative panel</a>";
    
    $orderssql = "SELECT * FROM orders WHERE status = 2";
    $ordersres = mysqli_query($db, $orderssql);
    $numrows = mysqli_num_rows($ordersres);
    
    if($numrows == 0) {
        echo "<p><strong>There are no orders currently.</strong>";
    }
    else {                
        echo "<table cellspacing=10>";
        
        while($row = mysqli_fetch_assoc($ordersres)) {
            echo "<tr>";
                echo "<td>[<a href='adminorderdetails.php?id=" . htmlspecialchars($row['id']) . "'>View</a>]</td>";
                echo "<td>" . date("D jS F Y g.iA", strtotime($row['date'])) . "</td>";
                echo "<td>";
                
                if($row['registered'] == 1) {
                    echo "Registered Customer";
                }
                else {
                    echo "Non-Registered Customer";
                }
                
                echo "</td>";
                echo "<td>&pound;" . sprintf('%.2f', $row['total']) . "</td>";
                echo "<td>";
                
                if($row['payment_type'] == 1) {
                    echo "PayPal";
                }
                else {
                    echo "Cheque";
                }
                
                echo "</td>";
                echo "<td><a href='adminorders.php?func=conf&id=" . htmlspecialchars($row['id']) . "'>Confirm Payment</a></td>";
            echo "</tr>";    
        }

        echo "</table>";
    }
}

require("footer.php");
?>