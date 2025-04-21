<?php
session_start();
require("db.php");
require("header.php");

// Check order status
$statussql = "SELECT status FROM orders WHERE id = ?";
$stmt = mysqli_prepare($db, $statussql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_ORDERNUM']);
mysqli_stmt_execute($stmt);
$statusres = mysqli_stmt_get_result($stmt);
$statusrow = mysqli_fetch_assoc($statusres);
mysqli_stmt_close($stmt);
$status = $statusrow['status'];

if($status == 1) {    
    header("Location: " . $config_basedir . "checkout-pay.php");
    exit;
}

if($status >= 2) {    
    header("Location: " . $config_basedir);
    exit;
}

// Check if form was submitted
if(($_POST['submit'] ?? '') != '') {
    // Check if user is logged in
    if(isset($_SESSION['SESS_LOGGEDIN'])) {
        if(($_POST['addselecBox'] ?? '') == '2') {
            // Validate required fields
            $required = ['forenameBox', 'surnameBox', 'add1Box', 'postcodeBox', 'phoneBox', 'emailBox'];
            foreach($required as $field) {
                if(empty($_POST[$field])) {
                    header("Location: " . $config_basedir . "checkout-address.php?error=1");
                    exit;
                }
            }
            
            // Sanitize inputs
            $fields = [
                'forename' => $_POST['forenameBox'],
                'surname' => $_POST['surnameBox'],
                'add1' => $_POST['add1Box'],
                'add2' => $_POST['add2Box'] ?? '',
                'add3' => $_POST['add3Box'] ?? '',
                'postcode' => $_POST['postcodeBox'],
                'phone' => $_POST['phoneBox'],
                'email' => $_POST['emailBox']
            ];
            
            // Insert address using prepared statement
            $addsql = "INSERT INTO delivery_addresses(forename, surname, add1, add2, add3, postcode, phone, email)
                      VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $addsql);
            mysqli_stmt_bind_param($stmt, "ssssssss", 
                $fields['forename'], $fields['surname'], $fields['add1'], 
                $fields['add2'], $fields['add3'], $fields['postcode'], 
                $fields['phone'], $fields['email']);
            mysqli_stmt_execute($stmt);
            $address_id = mysqli_insert_id($db);
            mysqli_stmt_close($stmt);

            // Update order
            $setaddsql = "UPDATE orders SET delivery_add_id = ?, status = 1 WHERE id = ?";
            $stmt = mysqli_prepare($db, $setaddsql);
            mysqli_stmt_bind_param($stmt, "ii", $address_id, $_SESSION['SESS_ORDERNUM']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: " . $config_basedir . "checkout-pay.php");
            exit;
        }
        else {
            // Use account address
            $custsql = "UPDATE orders SET delivery_add_id = 0, status = 1 WHERE id = ?";
            $stmt = mysqli_prepare($db, $custsql);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_ORDERNUM']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            header("Location: " . $config_basedir . "checkout-pay.php");
            exit;
        }
    }
    else {
        // Guest checkout - validate required fields
        $required = ['forenameBox', 'surnameBox', 'add1Box', 'postcodeBox', 'phoneBox', 'emailBox'];
        foreach($required as $field) {
            if(empty($_POST[$field])) {
                header("Location: checkout-address.php?error=1");
                exit;
            }
        }
        
        // Sanitize inputs
        $fields = [
            'forename' => $_POST['forenameBox'],
            'surname' => $_POST['surnameBox'],
            'add1' => $_POST['add1Box'],
            'add2' => $_POST['add2Box'] ?? '',
            'add3' => $_POST['add3Box'] ?? '',
            'postcode' => $_POST['postcodeBox'],
            'phone' => $_POST['phoneBox'],
            'email' => $_POST['emailBox']
        ];
        
        // Insert address
        $addsql = "INSERT INTO delivery_addresses(forename, surname, add1, add2, add3, postcode, phone, email)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $addsql);
        mysqli_stmt_bind_param($stmt, "ssssssss", 
            $fields['forename'], $fields['surname'], $fields['add1'], 
            $fields['add2'], $fields['add3'], $fields['postcode'], 
            $fields['phone'], $fields['email']);
        mysqli_stmt_execute($stmt);
        $address_id = mysqli_insert_id($db);
        mysqli_stmt_close($stmt);

        // Update order
        $setaddsql = "UPDATE orders SET delivery_add_id = ?, status = 1 WHERE session = ?";
        $stmt = mysqli_prepare($db, $setaddsql);
        $session_id = session_id();
        mysqli_stmt_bind_param($stmt, "is", $address_id, $session_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $config_basedir . "checkout-pay.php");
        exit;
    }
}
else {
    echo "<h1>Add a delivery address</h1>";
    echo "<a href='catalog.php'><--- Continue shopping</a>";
    echo "<p>";
    echo "<a href='showcart.php'><--- Review your shopping cart</a>";
    echo "<p>";
    
    if(isset($_GET['error'])) {
        echo "<strong>Please fill in the missing information...</strong>";
    }
    
    echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' onsubmit='return formValidator()'>";
    
    if(isset($_SESSION['SESS_LOGGEDIN'])) {
        echo '<input type="radio" name="addselecBox" value="1" checked>Use the address from my account</input><br>';
        echo '<input type="radio" name="addselecBox" value="2">Use the address below:</input>';
    }
?>
    
<script type='text/javascript'>
// Updated validation with more permissive patterns
function formValidator(){
    var forenameBox = document.getElementById('forenameBox');
    var surnameBox = document.getElementById('surnameBox');
    var add1Box = document.getElementById('add1Box');
    var postcodeBox = document.getElementById('postcodeBox');
    var phoneBox = document.getElementById('phoneBox');
    var emailBox = document.getElementById('emailBox');
    
    if(!notEmpty(forenameBox, "Please enter your forename")){
        return false;
    }
    if(!notEmpty(surnameBox, "Please enter your surname")){
        return false;
    }
    if(!notEmpty(add1Box, "Please enter your address")){
        return false;
    }
    if(!notEmpty(postcodeBox, "Please enter your postcode")){
        return false;
    }
    if(!notEmpty(phoneBox, "Please enter your phone number")){
        return false;
    }
    if(!emailValidator(emailBox, "Please enter a valid email address")){
        return false;
    }
    
    return true;
}

function notEmpty(elem, helperMsg){
    if(elem.value.trim() === ""){
        alert(helperMsg);
        elem.focus();
        return false;
    }
    return true;
}

function emailValidator(elem, helperMsg){
    var emailExp = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(elem.value.match(emailExp)){
        return true;
    }else{
        alert(helperMsg);
        elem.focus();
        return false;
    }
}
</script>

<table>
<tr>
    <td>Forename*</td>
    <td><input type="text" name="forenameBox" id="forenameBox" required></td>
</tr>
<tr>
    <td>Surname*</td>
    <td><input type="text" name="surnameBox" id="surnameBox" required></td>
</tr>
<tr>
    <td>House Number/Street*</td>
    <td><input type="text" name="add1Box" id="add1Box" required></td>
</tr>
<tr>
    <td>Town/City</td>
    <td><input type="text" name="add2Box" id="add2Box"></td>
</tr>
<tr>
    <td>County/State</td>
    <td><input type="text" name="add3Box" id="add3Box"></td>
</tr>
<tr>
    <td>Postcode*</td>
    <td><input type="text" name="postcodeBox" id="postcodeBox" required></td>
</tr>
<tr>
    <td>Phone*</td>
    <td><input type="text" name="phoneBox" id="phoneBox" required></td>
</tr>
<tr>
    <td>Email*</td>
    <td><input type="email" name="emailBox" id="emailBox" required></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Add Address"></td>
</tr>
</table>
</form>

<?php
}
require("footer.php");
?>