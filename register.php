<?php
session_start();

require("config.php");

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function is_valid_password($password) {
    // At least 8 characters, containing uppercase, lowercase and numbers
    return (strlen($password) >= 8 && 
            preg_match('/[A-Z]/', $password) && 
            preg_match('/[a-z]/', $password) && 
            preg_match('/[0-9]/', $password));
}

// Database connection using PDO
try {
    $dsn = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $dbuser, $dbpassword, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$errors = [];

// Process form submission
if (isset($_POST['submit'])) {
    // Validate required fields
    $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $password1 = isset($_POST['password1']) ? $_POST['password1'] : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    
    if (empty($username) || empty($password1) || empty($password2) || empty($email)) {
        header("Location: " . $config_basedir . "register.php?error=empty");
        exit;
    }
    
    // Validate email
    if (!is_valid_email($email)) {
        header("Location: " . $config_basedir . "register.php?error=email");
        exit;
    }
    
    // Validate password strength
    if (!is_valid_password($password1)) {
        header("Location: " . $config_basedir . "register.php?error=weak_password");
        exit;
    }
    
    // Check if passwords match
    if ($password1 !== $password2) {
        header("Location: " . $config_basedir . "register.php?error=pass");
        exit;
    }
    
    // Check if username is already taken
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: " . $config_basedir . "register.php?error=taken");
        exit;
    } else {
        // Generate verification token
        $verifyToken = bin2hex(random_bytes(32));
        
        // Hash password
        $passwordHash = password_hash($password1, PASSWORD_DEFAULT);
        
        // Insert user data
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, verifystring, active) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$username, $passwordHash, $email, $verifyToken]);
        
        // Construct verification URL
        $verifyUrl = $config_basedir . "verify.php";
        $verifyQueryString = http_build_query([
            'email' => $email,
            'verify' => $verifyToken
        ]);
        $completeVerifyUrl = $verifyUrl . '?' . $verifyQueryString;
        
        // Prepare email content
        $mail_body = <<<_MAIL_
Hi {$username},

Your account has been created in Zone-5 Computing Company website. Please click on the following link to verify your new account:

{$completeVerifyUrl}

This link will expire in 24 hours.

_MAIL_;
        
        // Send verification email
        $mail_sent = mail($email, $config_sitename . " User verification", $mail_body);
        
        // Show confirmation message
        require("header.php");
        echo "<div class='message success'>A link has been emailed to the address you provided. Please follow the link in the email to validate your account.</div>";
        require("footer.php");
        exit;
    }
} else {
    // Display registration form
    require("header.php");
    ?>
    <div class="form-container">
        <h1>Register</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php
                switch($_GET['error']) {
                    case "email":
                        echo "That is an invalid email address. Please enter a valid email address."; 
                        break;
                    case "empty":
                        echo "Please fill in all required fields.";
                        break;
                    case "pass":
                        echo "Passwords do not match!";
                        break;
                    case "taken":
                        echo "This username has already been taken, please use another.";
                        break;
                    case "weak_password":
                        echo "Password must be at least 8 characters long and include uppercase letters, lowercase letters, and numbers.";
                        break;
                    case "no":
                        echo "Incorrect registration details!";
                        break;
                    default:
                        echo "An error occurred. Please try again.";
                }
                ?>
            </div>
        <?php endif; ?>
        
        <p>To register on the <?php echo htmlspecialchars($config_sitename); ?> website, please fill in the form below.</p>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password1">Password</label>
                <input type="password" id="password1" name="password1" required>
                <small>Must be at least 8 characters with uppercase, lowercase letters and numbers</small>
            </div>
            
            <div class="form-group">
                <label for="password2">Confirm Password</label>
                <input type="password" id="password2" name="password2" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" value="1">JOIN US</button>
            </div>
        </form>
    </div>
    <?php
    require("footer.php");
}
?>