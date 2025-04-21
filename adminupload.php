<?php
session_start();

// Check if administrator is logged in
if(!isset($_SESSION['SESS_ADMINLOGGEDIN'])) {
    header("Location: " . $config_basedir . "adminlogin.php");
    exit;
}

// Specify maximum file size
$MAX_FILE_SIZE = 35000; 

// Check if submit button was clicked
if(($_POST['submit'] ?? '') != '') {
    // Validate file upload
    if(empty($_FILES['userfile']['name'])) {
        header("Location: " . $config_basedir . "adminupload.php?error=nophoto");
        exit;
    }
    elseif($_FILES['userfile']['size'] == 0) {
        header("Location: " . $config_basedir . "adminupload.php?error=photoprob");
        exit;
    }
    elseif($_FILES["userfile"]["type"] != "image/jpeg") {
        header("Location: " . $config_basedir . "adminupload.php?error=imagetype");
        exit;
    }
    elseif($_FILES['userfile']['size'] > $MAX_FILE_SIZE) {
        header("Location: " . $config_basedir . "adminupload.php?error=large");
        exit;
    }
    elseif(!getimagesize($_FILES['userfile']['tmp_name'])) {
        header("Location: " . $config_basedir . "adminupload.php?error=invalid");
        exit;
    }
    else {
        $uploaddir = "C:/xampplite/htdocs/shoppingcart/images/";
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
       
        if (file_exists($uploadfile)) {
            header("Location: " . $config_basedir . "adminupload.php?error=exists");
            exit;
        }
       
        if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            header("Location: " . $config_basedir . "adminhome.php");
            exit;
        }
        else {
            echo 'There was a problem uploading your file.<br />';
        }
    }     
}
else {
    require("header.php");
    echo "<h1>Upload product images</h1>";
    echo "<p>";
    echo "<a href='adminhome.php'><--- Return to the administrative panel</a>";
    echo "<p>";
}

// Error messages
switch($_GET['error'] ?? '') {
    case "empty":
        echo 'You did not select anything.';
        break;
    case "nophoto":
        echo 'You did not select an image to upload.';
        break;
    case "photoprob":
        echo 'There appears to be a problem with the image you are uploading';
        break;
    case "large":
        echo 'The image you selected is too large';
        break;
    case "invalid":
        echo 'The image you selected is not a valid image file';
        break;
    case "exists":
        echo 'The image you are trying to upload already exists. Please try to upload another image file.';
        break;
    case "imagetype":
        echo 'Please select an image which is in a jpeg format';
        break;
}
?>

<form enctype="multipart/form-data" action="adminupload.php" method="POST">
<table>
<tr>
   <td>Image to upload</td>
   <td><input name="userfile" type="file" accept="image/jpeg"></td>
</tr>
<tr>
    <td><input type="submit" name="submit" value="Upload File"></td>
</tr>
</table>
</form>

<?php
require("footer.php");
?>