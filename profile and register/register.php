<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, surname, username, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $surname, $username, $email, $hashed_password]);
            header('Location: Login Page.html');
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Finance App Registration</title>
    <link rel="stylesheet" href="registration page.css" />
</head>
<body>
    <div class="registration-container">
        <div class="registration-box">
            <h2>Create an Account</h2>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="name">First Name:</label>
                    <input type="text" id="name" name="name" required /><br />
                </div>
                <div class="input-group">
                    <label for="surname">Last Name:</label>
                    <input type="text" id="surname" name="surname" required /><br />
                </div>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required /><br />
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required /><br />
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required /><br />
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required /><br />
                </div>
                <div class="input-group">
                    <button type="submit">Register</button>
                </div>
                <div class="extra-links">
                    <a href="Login page.html">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

