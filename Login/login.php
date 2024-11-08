<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Database connection
        $pdo = new PDO('mysql:host=localhost;dbname=transactions_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify user and password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: ../Dashboard/index.php');
            exit();
        } else {
            header('Location: Login page.html?error=Invalid+username+or+password');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: Login page.html?error=An+error+occurred.+Please+try+again+later.');
        exit();
    }
}
