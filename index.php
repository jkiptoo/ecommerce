<?php
require("header.php");
?>

<h1>Welcome!!</h1>
<p>Welcome to the <strong><?php echo htmlspecialchars($config_sitename); ?></strong> website. Click on the links in this page to explore this website.
There is a wide range of products on display in this website. Feel free to move around the site.</p>

<h2>What we sell...</h2>
<div class="product-highlight">
    <p>We offer a wide range of computing products including:</p>
    <ul>
        <li>Laptops and Desktop Computers</li>
        <li>Computer Accessories</li>
        <li>Software Solutions</li>
        <li>Networking Equipment</li>
    </ul>
    <p>Browse our <a href="catalog.php">product catalog</a> to see our full range.</p>
</div>

<?php
require("footer.php");
?>