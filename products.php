<?php
	require("db.php");
	require("functions.php");

//validate the 'GET' variable from the home page
	$validid = pf_validate_number($_GET['id'], "redirect", $config_basedir);

	require("header.php");
	
	//sql statement to retrieve a product whose category id matches the one passed to the page by the 'GET' id from the home page
	$prodcatsql = "SELECT * FROM products WHERE cat_id = " . $_GET['id'] . ";";
	$prodcatres = mysqli_query($prodcatsql);
	$numrows = mysqli_num_rows($prodcatres);		

	//if no product results are returned, display error message indicating the selected category has no products
	if($numrows == 0)
	{
		echo "<h1>No Products</h1>";
		echo "Currently, there are no products in this category. Please come back later.";
	}
	
	//if the query returns results, then display the products in the selected category
	else
	{
	
		echo "<table cellpadding='10'>";
		
		while($prodrow = mysqli_fetch_assoc($prodcatres))
		{
			
			//if the product has no image, display a default/dummy image
			echo "<tr>";
				if(empty($prodrow['image'])) {
					echo "<td><img src='./images/No Image.jpg' alt='" . $prodrow['name'] . "'></td>";
				}
				
				//if the product has an image available, retrieve and display the product image
				else {
					echo "<td><img src='./images/" . $prodrow['image'] . "' alt='" . $prodrow['name'] . "'></td>";
				}
				
				//display more detailed information about each of the products
				echo "<td>";
					echo "<h2>" . $prodrow['name'] . "</h2>";
					echo "<p>" . $prodrow['description'];
					
					//display the price of the product(s) and 'ADD TO CART' link
					echo "<p><strong>OUR PRICE: &pound;" . sprintf('%.2f', $prodrow['price']) . "</strong>";
					echo "<p><a href='addtobasket.php?id=" . $prodrow['id'] . "'>BUY PRODUCT</a>";
				echo "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}

	require("footer.php");
?>
	
