<?php
session_start();
require("config.php");
require("db.php");

// Redirect if already logged in
if(isset($_SESSION['SESS_LOGGEDIN']) && $_SESSION['SESS_LOGGEDIN'] == 1) {
    header("Location: " . $config_basedir);
    exit;
}

// Process login form
if(isset($_POST['submit'])) {
    // Validate inputs
    if(empty($_POST['userBox']) || empty($_POST['passBox'])) {
        header("Location: " . $config_basedir . "login.php?error=2");
        exit;
    }

    // Get user with prepared statement
    $loginsql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($db, $loginsql);
    mysqli_stmt_bind_param($stmt, "s", $_POST['userBox']);
    mysqli_stmt_execute($stmt);
    $loginres = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($loginres) == 1) {
        $loginrow = mysqli_fetch_assoc($loginres);
        
        // Verify password (assuming passwords are hashed)
        if(password_verify($_POST['passBox'], $loginrow['password'])) {
            // Set session variables
            $_SESSION['SESS_LOGGEDIN'] = 1;
            $_SESSION['SESS_USERNAME'] = $loginrow['username'];
            $_SESSION['SESS_USERID'] = $loginrow['id'];
            
            // Get existing order if any
            $ordersql = "SELECT id FROM orders WHERE customer_id = ? AND status < 2";
            $stmt = mysqli_prepare($db, $ordersql);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_USERID']);
            mysqli_stmt_execute($stmt);
            $orderres = mysqli_stmt_get_result($stmt);
            $orderrow = mysqli_fetch_assoc($orderres);
            mysqli_stmt_close($stmt);
            
            if($orderrow) {
                $_SESSION['SESS_ORDERNUM'] = $orderrow['id'];
            }
            
            header("Location: " . $config_basedir . "showcart.php");
            exit;
        }
    }
    
    // Login failed
    header("Location: " . $config_basedir . "login.php?error=1");
    exit;
}

// Show login form
require("header.php");
?>
<h1>Customer Login</h1>
<p>Please enter your username and password to log into this website. If you do not have an account with us, you can get one for free by clicking <a href="register.php">HERE</a>.</p>

<?php
if(isset($_GET['error'])) {
    switch($_GET['error']) {
        case 1:
            echo "<div class='error'>Incorrect login details. Please try again.</div>";
            break;
        case 2:
            echo "<div class='error'>Please fill in the required fields.</div>";
            break;
    }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <table align="center">
        <tr>
            <td>Username</td>
            <td><input type="text" name="userBox" required></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input type="password" name="passBox" required></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submit" value="SIGN IN"></td>
        </tr>        
    </table>
</form>

<?php
require("footer.php");
?>