<?php
session_start();
require("db.php");
require("functions.php");
require("header.php");

// Check if PayPal button was clicked
if(($_POST['paypalsubmit'] ?? '') != '') {
    // Update order status
    $upsql = "UPDATE orders SET status = 2, payment_type = 1 WHERE id = ?";
    $stmt = mysqli_prepare($db, $upsql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_ORDERNUM']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Get order total
    $itemssql = "SELECT total FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($db, $itemssql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_ORDERNUM']);
    mysqli_stmt_execute($stmt);
    $itemsres = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($itemsres);
    mysqli_stmt_close($stmt);

    // Clear session
    if(isset($_SESSION['SESS_LOGGEDIN'])) {
        unset($_SESSION['SESS_ORDERNUM']);
    }
    else {
        $_SESSION['SESS_CHANGEID'] = 1;
    }

    // Redirect to PayPal
    $paypal_url = "https://www.paypal.com/cgi-bin/webscr?" . 
                 "cmd=_xclick&business=" . urlencode($config_paypal_email) . 
                 "&item_name=" . urlencode($config_sitename . " Order") . 
                 "&item_number=PROD" . $row['id'] .
                 "&amount=" . urlencode(sprintf('%.2f', $row['total'])) . 
                 "&no_note=1&currency_code=GBP&lc=GB";
    
    header("Location: " . $paypal_url);
    exit;
}
// Check if Cheque button was clicked
else if(($_POST['chequesubmit'] ?? '') != '') {
    // Update order status
    $upsql = "UPDATE orders SET status = 2, payment_type = 2 WHERE id = ?";
    $stmt = mysqli_prepare($db, $upsql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_ORDERNUM']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Clear session
    if(isset($_SESSION['SESS_LOGGEDIN'])) {
        unset($_SESSION['SESS_ORDERNUM']);
    }
    else {
        $_SESSION['SESS_CHANGEID'] = 1;
    }

    echo "<h1>PAYING BY CHEQUE</h1>";
    echo "Please make your cheque payable to <strong>" . htmlspecialchars($config_sitename) . "</strong>.";
    echo "<p>";
    echo "Send the cheque to:";
    echo "<p>";
    echo htmlspecialchars($config_sitename) . "<br>";
    echo "345, Zone-5 Computing Company,<br>";
    echo "Kenyatta Avenue,<br>";
    echo "Nairobi,<br>";
    echo "Nairobi County.<br>";
}
else {
    echo "<h1>Payment</h1>";
    showcart();
?>
    <h2>Select a payment method</h2>
    <form action='checkout-pay.php' method='POST'>
    <table cellspacing=10>
    <tr>
        <td><h3>PayPal</h3></td>
        <td>
        Our website uses PayPal to accept Switch/Visa/Mastercard cards. No
        PayPal account is required - you simply fill in your credit card details
        and the correct payment will be deducted from your account.
        </td>
        <td><input type="submit" name="paypalsubmit" value="Pay with PayPal"></td>
    </tr>
    <tr>
        <td><h3>Cheque</h3></td>
        <td>
        If you would like to pay by cheque, you can post the cheque for the final
        amount to our office.
        </td>
        <td><input type="submit" name="chequesubmit" value="Pay by cheque"></td>
    </tr>
    </table>
    </form>
<?php
}
require("footer.php");
?>