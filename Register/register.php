<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "";
    
    try {
        require_once "../db.php";
        
        // Get and validate form data
        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($name) || empty($surname) || empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }
        
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Password requirements check
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: Registrationpage.html?error=" . urlencode("Username already exists"));
            exit();
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: Registrationpage.html?error=" . urlencode("Email already exists"));
            exit();
        }
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, name, surname, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $name, $surname, $email, $hashed_password]);
        
        // Redirect to login page on success
        header('Location: ../Login/Login Page.html');
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    } catch (PDOException $e) {
        $error = "Database error: Please try again later";
        // For debugging (remove in production):
        // $error = "Error: " . $e->getMessage();
    }
    
    // If there's an error, redirect back with error message
    if (!empty($error)) {
        header("Location: Registrationpage.html?error=" . urlencode($error));
        exit();
    }
}
?>