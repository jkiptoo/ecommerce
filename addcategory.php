<?php

session_start();

require("db.php");

//if administrator is not logged in, redirect page to administrator login page
if(isset($_SESSION['SESS_ADMINLOGGEDIN']) == FALSE) {
	header("Location: " . $config_basedir . "adminlogin.php");
}

//check if the submit upload button has been clicked
if($_POST['submit']) {
	
	//if the category field is empty, display error message
	if(empty($_POST['category']))
	{
			header("Location: " .$config_basedir. "addcategory.php?error=1");
			exit;
		}
		
		//add category into categories table
		else
		{
	$subsql = "INSERT INTO categories(name) VALUES('"
				. $_POST['category']
				. "')";
	mysqli_query($subsql);
	header("Location: " . $config_basedir . "adminhome.php");
}
}
else {
	require("header.php");
	echo "<h1>Add a new product category</h1>";
	echo "<a href='adminhome.php'><--- Return to the administrative panel</a>";
	echo "<p>";
	if ($_GET['error'] == 1) {
		echo "<strong>Please fill in the required field!</strong>";
	}

?>
	<p>
   <?php echo "<form action='" . $SCRIPT_NAME . "' method='POST' onsubmit='return formValidator()' >";?>
    <script type='text/javascript'>
//validate user input
function __construct()
	// Make quick references to our fields
	var category = document.getElementById('category');
	
	// Check each input in the order that it appears in the form!
	if(isAlphabet(category, "Please enter only letters for the category field")){
		return true;
		}
		return false;
		}

//ensure all fields are not empty
function __construct(elem, helperMsg)
	if(elem.value.length == 0){
		alert(helperMsg);
		elem.focus(); // set the focus to this input
		return false;
	}
	return true;
}

//function to validate the input alphabetical fields 
function __construct(elem, helperMsg)
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
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
	<td><input type="text" name="category" id="category"></td>
	</tr>
    <tr>
    <td></td>
	<td><input type="submit" name="submit" value="Add Category"></td>
	</tr>
	</table>
	</form>

<?php
}

require("footer.php");

?>