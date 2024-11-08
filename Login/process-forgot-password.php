<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=transactions_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate simple reset code
            $reset_code = substr(str_shuffle("0123456789"), 0, 6);
            
            // Store reset code in database
            $stmt = $pdo->prepare("UPDATE users SET reset_code = ? WHERE id = ?");
            $stmt->execute([$reset_code, $user['id']]);
            
            // Store username in session for the reset process
            $_SESSION['reset_username'] = $username;
            
            // Redirect to reset page
            header('Location: reset-password.php?code=' . $reset_code);
            exit();
        } else {
            header('Location: forgot-password.html?error=Username+not+found');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: forgot-password.html?error=An+error+occurred');
        exit();
    }
}
?>