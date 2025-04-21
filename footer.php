<?php
echo "<p><i>This is " . htmlspecialchars($config_sitename) . "</i></p>"; 

// Check if administrator is logged in
if(isset($_SESSION['SESS_ADMINLOGGEDIN']) && $_SESSION['SESS_ADMINLOGGEDIN'] == 1) {
    echo "[<a href='" . htmlspecialchars($config_basedir) . "adminhome.php'>ADMINISTRATOR</a>] ";
    echo "[<a href='" . htmlspecialchars($config_basedir) . "adminlogout.php'>ADMINISTRATOR LOGOUT</a>]";
}
?>

</div>
    </div>
</body>
</html>