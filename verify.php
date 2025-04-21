<?php
session_start();
require("config.php");

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

// Include header
require("header.php");

echo '<div class="container">';

// Check if required parameters exist
if (!isset($_GET['verify']) || !isset($_GET['email'])) {
    echo '<div class="message error-message">Verification failed: Missing parameters.</div>';
    require("footer.php");
    exit;
}

// Sanitize and validate input
$verifyToken = $_GET['verify'];
$email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo '<div class="message error-message">Verification failed: Invalid email format.</div>';
    require("footer.php");
    exit;
}

// Check if the token and email match a user in the database
try {
    // Find user with matching verification token and email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE verifystring = ? AND email = ? AND active = 0");
    $stmt->execute([$verifyToken, $email]);
    
    // Check if a user was found
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Activate the user account
        $updateStmt = $pdo->prepare("UPDATE users SET active = 1, verifystring = NULL WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Check if the update was successful
        if ($updateStmt->rowCount() == 1) {
            echo '<div class="message success">
                <h2>Account Verified Successfully!</h2>
                <p>Your account has now been verified. You can now <a href="login.php">log in</a> to your account.</p>
            </div>';
        } else {
            echo '<div class="message error-message">
                <h2>Verification Error</h2>
                <p>There was a problem activating your account. Please contact support.</p>
            </div>';
        }
    } else {
        // Check if the user is already verified
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND active = 1");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->rowCount() == 1) {
            echo '<div class="message success">
                <h2>Already Verified</h2>
                <p>This account has already been verified. You can <a href="login.php">log in</a> to your account.</p>
            </div>';
        } else {
            echo '<div class="message error-message">
                <h2>Verification Failed</h2>
                <p>The verification link is invalid or has expired. Please request a new verification link.</p>
            </div>';
        }
    }
} catch (PDOException $e) {
    echo '<div class="message error-message">
        <h2>Database Error</h2>
        <p>An error occurred while processing your request. Please try again later.</p>
    </div>';
    
    // Log the error (not visible to users)
    error_log("Verification error: " . $e->getMessage());
}

echo '</div>';

// Include footer
require("footer.php");
?>