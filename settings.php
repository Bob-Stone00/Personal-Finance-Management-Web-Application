<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_profile':
            $fullName = $_POST['fullName'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            
            try {
                $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE username = ?");
                $stmt->execute([$fullName, $email, $phone, $_SESSION['username']]);
                $_SESSION['message'] = "Profile updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
            }
            break;
            
        case 'update_preferences':
            $theme = $_POST['theme'] ?? 'light';
            $notifications = isset($_POST['notifications']) ? 1 : 0;
            $currency = $_POST['currency'] ?? 'R';
            
            try {
                $stmt = $conn->prepare("UPDATE user_preferences SET theme = ?, notifications = ?, currency = ? WHERE username = ?");
                $stmt->execute([$theme, $notifications, $currency, $_SESSION['username']]);
                $_SESSION['message'] = "Preferences updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating preferences: " . $e->getMessage();
            }
            break;
            
        case 'change_password':
            $currentPassword = $_POST['currentPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            
            try {
                if ($newPassword !== $confirmPassword) {
                    throw new Exception("New passwords do not match");
                }
                
                $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
                $stmt->execute([$_SESSION['username']]);
                $user = $stmt->fetch();
                
                if (!password_verify($currentPassword, $user['password'])) {
                    throw new Exception("Current password is incorrect");
                }
                
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->execute([$hashedPassword, $_SESSION['username']]);
                $_SESSION['message'] = "Password changed successfully!";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            break;
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $userData = $stmt->fetch();
    
    $stmt = $conn->prepare("SELECT * FROM user_preferences WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $userPreferences = $stmt->fetch();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching user data: " . $e->getMessage();
}
?>
