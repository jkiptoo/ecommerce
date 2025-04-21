<?php

//start session
	session_start();

	require("header.php");	
	require("functions.php");

	echo "<h1>Your shopping cart</h1>";
	echo "<a href='catalog.php'><--- Continue shopping</a>";
	
	//call the showcart function
	showcart();

//check if the order session is present
	if(isset($_SESSION['SESS_ORDERNUM']) == TRUE) {
		
			//retrieve order items whose order id matches the order sesssion variable passed on to the page
		$sql = "SELECT * FROM orderitems WHERE order_id = " . $_SESSION['SESS_ORDERNUM'] . ";";
		$result = mysqli_query($sql);
		$numrows = mysqli_num_rows($result);
		
			//check if the order items is equal to or greater than one
		if($numrows >= 1) {
			echo "<h2><a href='checkout-address.php'>Proceed to the checkout</a></h2>";
			
		}
	}
	
	
	require("footer.php");
?>