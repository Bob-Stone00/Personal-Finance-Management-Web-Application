<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../Login page.html'); // Redirect to login page if not logged in
    exit();
}

// Fetch the user details from the database
$username = $_SESSION['username'];
try {
    $stmt = $conn->prepare("SELECT name, surname, email FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $profileName = $user['name']; 
        $profileSurname = $user['surname'];
        $profileEmail = $user['email']; 
    } else {
        $profileName = "Unknown";
        $profileSurname = "User";
        $profileEmail = "unknown@example.com";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css" />
</head>
<body>
    <section class="navigation">
        <button onclick="history.back()">Go Back</button>
    </section>

    <div class="profile-container">
        <section class="profile-info">
            <h1>Profile Information</h1>
            <input type="file" id="fileInput" accept="image/*" onchange="previewImage(event)">
            <p>Name: <b><span id="profileName"><?php echo htmlspecialchars($profileName); ?></span></b></p>
            <p>Surname: <b><span id="profileSurname"><?php echo htmlspecialchars($profileSurname); ?></span></b></p>
            <p>Email: <b><span id="profileEmail"><?php echo htmlspecialchars($profileEmail); ?></span></b></p>
        </section>

        <!-- Profile Update Form -->
<section class="update-profile">
	 <button id="toggleButton">Edit</button>
    <form id="profileForm"  action="update.php" method="POST" style="display:none">        
        <input type="text" id="newName" name="name" placeholder="Enter your new name">       
        <input type="text" id="newSurname" name="surname" placeholder="Enter your new surname">
       <input type="email" id="newEmail" name="email" placeholder="Enter your new email">
        <button type="submit">Update Profile</button>
    </form>
</section>


        <section class="logout">
            <button onclick="logoutUser()">Logout</button>
        </section>
    </div>

    <script src="profile.js"></script>
</body>
</html>