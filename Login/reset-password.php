<!-- Modified reset-password.php -->
<?php
session_start();
include '../db.php';

if (!isset($_GET['code']) || !isset($_SESSION['reset_username'])) {
    header('Location: Login page.html');
    exit();
}

$reset_code = $_GET['code'];
$username = $_SESSION['reset_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="Login page.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Set New Password</h2>
            <p>Reset Code: <strong><?php echo htmlspecialchars($reset_code); ?></strong></p>
            <form method="post" action="update-password.php">
                <input type="hidden" name="reset_code" value="<?php echo htmlspecialchars($reset_code); ?>">
                <div class="input-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" id="new_password" required minlength="8" />
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="8" />
                </div>
                <div class="input-group">
                    <button type="submit">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>