<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['reset_username'])) {
        header('Location: Login page.html');
        exit();
    }

    $username = $_SESSION['reset_username'];
    $reset_code = $_POST['reset_code'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        header('Location: ./reset-password.php?code=' . urlencode($reset_code) . '&error=Passwords+do+not+match');
        exit();
    }
    
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=transactions_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify reset code and update password
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND reset_code = ?");
        $stmt->execute([$username, $reset_code]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Update password and clear reset code
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);
            
            // Clear session
            unset($_SESSION['reset_username']);
            
            header('Location: ./Login page.html?message=Password+updated+successfully');
            exit();
        } else {
            header('Location: ./forgot-password.html?error=Invalid+reset+code');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ./forgot-password.html?error=An+error+occurred');
        exit();
    }
}
?>