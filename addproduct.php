<?php
session_start();
require("db.php");
require("header.php");

// Check if administrator is logged in
if(!isset($_SESSION['SESS_ADMINLOGGEDIN'])) {
    header("Location: " . $config_basedir . "adminlogin.php");
    exit;
}

// Check if form was submitted
if(($_POST['submit'] ?? '') != '') {
    // Validate required fields
    if(empty($_POST['category']) ||
       empty($_POST['name']) ||
       empty($_POST['description']) ||
       empty($_POST['price'])) {
        header("Location: " . $config_basedir . "addproduct.php?error=1");
        exit;
    }
    
    // Sanitize inputs
    $category = intval($_POST['category']);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $image = mysqli_real_escape_string($db, $_POST['image'] ?? '');
    $price = floatval($_POST['price']);

    // Insert product using prepared statement
    $prodsql = "INSERT INTO products(cat_id, name, description, image, price) VALUES(?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $prodsql);
    mysqli_stmt_bind_param($stmt, "isssd", $category, $name, $description, $image, $price);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $config_basedir . "adminupload.php");
    exit;
}
else {
    echo "<h1>Add a new product</h1>";
    echo "<a href='adminhome.php'><--- Return to the administrative panel</a>";
    echo "<p>";
    
    if (($_GET['error'] ?? '') == 1) {
        echo "<strong>Please fill in the required fields!</strong>";
    }
?>

<p>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method='POST' onsubmit='return formValidator()'>
    
<script type='text/javascript'>
// Updated form validation with more permissive patterns
function formValidator(){
    var name = document.getElementById('name');
    var description = document.getElementById('description');
    var price = document.getElementById('price');
    
    if(!notEmpty(name, "Please enter a product name")){
        return false;
    }
    if(!notEmpty(description, "Please enter a product description")){
        return false;
    }
    if(!isNumeric(price, "Please enter a valid price")){
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

function isNumeric(elem, helperMsg){
    var numericExpression = /^[0-9]+(\.[0-9]{1,2})?$/;
    if(elem.value.match(numericExpression)){
        return true;
    }else{
        alert(helperMsg);
        elem.focus();
        return false;
    }
}
</script>

<table cellpadding="5">
<tr>
    <td>Category</td>
    <td>
    <?php
    if(isset($_SESSION['SESS_ADMINLOGGEDIN'])) {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = mysqli_query($db, $sql);
        
        echo "<select name='category'>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
        }
        echo "</select>";
    }
    ?>
    </td>
</tr>
<tr>
    <td>Product name</td>
    <td><input type="text" name="name" id="name" required></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="description" required></textarea></td>
</tr>
<tr>
    <td>Image (e.g. compaq.jpg) - Optional</td>
    <td><input type="text" name="image" id="image"></td>
</tr>
<tr>
    <td>Product Price (In GBP)</td>
    <td><input type="text" name="price" id="price" required></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Add Product"></td>
</tr>
</table>
</form>

<?php
}
require("footer.php");
?>