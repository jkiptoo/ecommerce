<?php
	session_start();

	require("db.php");
	
	//check if the administrator is already logged in, if true, redirect the page to the website's home page
	if(isset($_SESSION['SESS_ADMINLOGGEDIN']) == TRUE) {
		header("Location: " . $config_basedir);
	}
	
	//if the user clicks the 'SIGN IN' button, check the records in the 'admin' table matches the input by the user
	if($_POST['submit'])
	{

                //check if the user has submitted an empty form
		if(empty($_POST['userBox']) ||
		empty($_POST['passBox']))
		{
			header("Location: " .$config_basedir. "adminlogin.php?error=1");
			exit;
		}
	
	{
		$loginsql = "SELECT * FROM admin WHERE username = '" . $_POST['userBox'] . "' AND password = '" . $_POST['passBox'] . "'";
		$loginres = mysqli_query($loginsql);
		$numrows = mysqli_num_rows($loginres);
		
		//if the rows returned is equal to one, log in the administrator
		if($numrows == 1)
		{
			$loginrow = mysqli_fetch_assoc($loginres);
			
			//create new session variable called 'SESS_ADMINLOGGEDIN'
			session_register("SESS_ADMINLOGGEDIN");
			session_register("SESS_ADMINUSERNAME");
			session_register("SESS_ADMINUSERID");
			
			$_SESSION['SESS_ADMINLOGGEDIN'] = 1;
			$_SESSION['SESS_ADMINUSERNAME'] = $loginrow['username'];
			$_SESSION['SESS_ADMINUSERID'] = $loginrow['id'];
			

			//redirect the page to the administrative screen
			header("Location: " . $config_basedir  . "adminhome.php");

		}
		
		//if the login details supplied by the user do not match the records in the database, issue an error message
		else
		{
			header("Location: " . $config_basedir  . "adminlogin.php?error=2");
		}
	}
	}
	else
	{

	require("header.php");
		
	echo "<h1>Administrator Login</h1>";
	

        //if user has submitted an empty form, display error message 
	if($_GET['error'] == 1) {
		echo "<strong>Please fill in the required fields!</strong>";
		}

                //if user has submitted incorrect login details, display error                //message
		if($_GET['error'] == 2) {
			echo "<strong>Incorrect username and/or password! Please try again</strong>";
		}
	
?>
	<p>
	<form action="<?php echo $SCRIPT_NAME; ?>" method="POST">
	<table>
		<tr>
			<td>Username</td>
			<td><input type="textbox" name="userBox">
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="passBox">
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="SIGN IN">
		</tr>		
	</table>
	</form>
	
<?php
	}
	
	require("footer.php");
?>
	
