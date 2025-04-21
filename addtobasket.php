<?php

	session_start();
	
	require("db.php");
	require("functions.php");

	$validid = pf_validate_number($_GET['id'], "redirect", $config_basedir);

	//sql statement to retrieve products whose id matches the one passed by the 'GET' variable id from the products page 
	$prodsql = "SELECT * FROM products WHERE id = " . $validid . ";";
	$prodres = mysqli_query($prodsql);
	$numrows = mysqli_num_rows($prodres);		
	$prodrow = mysqli_fetch_assoc($prodres);

	if($numrows == 0)
	{
		header("Location: " . $config_basedir);
	}
	else
	{
		
		//if no results are returned, redirect to the website's base URL
		if($_POST['submit'])
		{
			
			//reload page and check if the 'SESS_ORDERNUM' session variable exists
			if($_SESSION['SESS_ORDERNUM'])
			{
				
				//if the 'SESS_ORDERNUM' exists, a previous order exists, the sql statement below adds the product id and quantity to the 'orderitems' table whose order_id is the 'SESS_ORDERNUM' variable
					$itemsql = "INSERT INTO orderitems(order_id, product_id, quantity) VALUES("
						. $_SESSION['SESS_ORDERNUM'] . ", " . $validid . ", "
						. $_POST['amountBox'] . ")";
					mysqli_query($itemsql);
			}
			else
			{
				
				//check if the current user is logged in, if true, create an order then add the item and its quantity to the 'orderitems' table
				if($_SESSION['SESS_LOGGEDIN'])
				{
					$sql = "INSERT INTO orders(customer_id, registered, date) VALUES("
							. $_SESSION['SESS_USERID'] . ", 1, NOW())";
					mysqli_query($sql);
					session_register("SESS_ORDERNUM");
					$_SESSION['SESS_ORDERNUM'] = mysql_insert_id();
					
					$itemsql = "INSERT INTO orderitems(order_id, product_id, quantity) VALUES("
						. $_SESSION['SESS_ORDERNUM'] . ", " . $validid . ", "
						. $_POST['amountBox'] . ")";

					mysqli_query($itemsql);
				}
				else
				{
					
					//if the user is not logged in, create an order in the 'orders' table using a unique session id, then add the item to the orderitems table
					$sql = "INSERT INTO orders(registered, date, session) VALUES("
							. "0, NOW(), '" . session_id() . "')";
					mysqli_query($sql);
					session_register("SESS_ORDERNUM");
					$_SESSION['SESS_ORDERNUM'] = mysql_insert_id();
					
					$itemsql = "INSERT INTO orderitems(order_id, product_id, quantity) VALUES("
						. $_SESSION['SESS_ORDERNUM'] . ", " . $validid . ", "
						. $_POST['amountBox'] . ")";

					mysqli_query($itemsql);
				}					
			}

			//calculate the price of the order and update the 'totals' field in the orders table 
			$totalprice = $prodrow['price'] * $_POST['amountBox'] ;

			$updsql = "UPDATE orders SET total = total + " . $totalprice . " WHERE id = " . $_SESSION['SESS_ORDERNUM'] . ";";
			mysqli_query($updres);
			
			//redirect to 'showcart.php' page
			header("Location: " . $config_basedir . "showcart.php");
		}
		else
		{
			require("header.php");

			//pass the 'GET id' variable to the next page once the 'ADD TO CART' button has been clicked
			echo "<form action='addtobasket.php?id=" . $_GET['id'] . "' method='post'>";
			echo "<table cellpadding='10'>";
		
		
			echo "<tr>";
			
			//if the selected product has no image, display a dummy image with a reduced size
				if(empty($prodrow['image'])) {
					echo "<td><img src='./images/No Image.jpg' width='50' alt='" . $prodrow['name'] . "'></td>";
				}
				else {
					
					//if the selected product has an image, display its image with a reduced size
					echo "<td><img src='./images/" . $prodrow['image'] . "' width='50' alt='" . $prodrow['name'] . "'></td>";
				}

				//enable user to select quantity of the product to be ordered
				echo "<td>" . $prodrow['name'] . "</td>";
				echo "<td>Select Quantity <select name='amountBox'>";
			
				for($i=1;$i<=100;$i++)
				{
					echo "<option>" . $i . "</option>";
				}
			
				echo "</select></td>";
				echo "<td><strong>&pound;" . sprintf('%.2f', $prodrow['price']) . "</strong></td>";
				echo "<td><input type='submit' name='submit' value='Add to Cart'></td>";
			echo "</tr>";
							
			echo "</table>";
			echo "</form>";
		}
	}

	require("footer.php");
?>
	
