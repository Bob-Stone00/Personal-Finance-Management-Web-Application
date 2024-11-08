<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header('Location: ../Login/Login page.html');
        exit();
    }

    // Retrieve the username from the session
    $username = $_SESSION['username'];

    // Retrieve the new values from the form, if provided
    $newName = !empty($_POST['name']) ? $_POST['name'] : null;
    $newSurname = !empty($_POST['surname']) ? $_POST['surname'] : null;
    $newEmail = !empty($_POST['email']) ? $_POST['email'] : null;

    // Build the SQL query dynamically based on provided fields
    $query = "UPDATE users SET ";
    $params = [];
    
    if ($newName !== null) {
        $query .= "name = ?, ";
        $params[] = $newName;
    }

    if ($newSurname !== null) {
        $query .= "surname = ?, ";
        $params[] = $newSurname;
    }

    if ($newEmail !== null) {
        $query .= "email = ?, ";
        $params[] = $newEmail;
    }

    // Remove the trailing comma and space, and add the WHERE clause
    $query = rtrim($query, ", ") . " WHERE username = ?";
    $params[] = $username;

    // Execute the update query
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        // Redirect back to profile page or show a success message
        header('Location: profile.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>