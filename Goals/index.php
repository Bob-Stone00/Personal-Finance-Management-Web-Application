<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transactions_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$user_id = $_SESSION['user_id'] ?? null; // Retrieve the user_id from the session

// CRUD Functions
function addGoal($goal_name, $goal_amount, $current_amount, $user_id) {
    global $conn;

    $percentage_complete = ($current_amount / $goal_amount) * 100;
    $status = ($percentage_complete >= 100) ? "Completed" : "In Progress";

    $sql = "INSERT INTO financial_goals (goal_name, goal_amount, current_amount, percentage_complete, status, user_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdddsd", $goal_name, $goal_amount, $current_amount, $percentage_complete, $status, $user_id);

    return $stmt->execute();
}

function getAllGoals($user_id) {
    global $conn;

    $sql = "SELECT * FROM financial_goals WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $goals = array();
    while ($row = $result->fetch_assoc()) {
        $goals[] = $row;
    }

    return $goals;
}

function updateGoal($id, $goal_name, $goal_amount, $current_amount, $user_id) {
    global $conn;

    $percentage_complete = ($current_amount / $goal_amount) * 100;
    $status = ($percentage_complete >= 100) ? "Completed" : "In Progress";

    $sql = "UPDATE financial_goals SET goal_name = ?, goal_amount = ?, current_amount = ?, 
            percentage_complete = ?, status = ? WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddsiii", $goal_name, $goal_amount, $current_amount, $percentage_complete, $status, $id, $user_id);

    return $stmt->execute();
}

function deleteGoal($id, $user_id) {
    global $conn;

    $sql = "DELETE FROM financial_goals WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);

    return $stmt->execute();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];

    switch ($action) {
        case 'add':
            if ($user_id && addGoal($_POST['goal_name'], $_POST['goal_amount'], $_POST['current_amount'], $user_id)) {
                $response['success'] = true;
                $response['message'] = 'Goal added successfully';
            } else {
                $response['message'] = 'Error adding goal';
            }
            break;
        case 'update':
            if ($user_id && updateGoal($_POST['id'], $_POST['goal_name'], $_POST['goal_amount'], $_POST['current_amount'], $user_id)) {
                $response['success'] = true;
                $response['message'] = 'Goal updated successfully';
            } else {
                $response['message'] = 'Error updating goal';
            }
            break;
        case 'delete':
            if ($user_id && deleteGoal($_POST['id'], $user_id)) {
                $response['success'] = true;
                $response['message'] = 'Goal deleted successfully';
            } else {
                $response['message'] = 'Error deleting goal';
            }
            break;
        case 'getAll':
            if ($user_id) {
                $goals = getAllGoals($user_id);
                $response['success'] = true;
                $response['goals'] = $goals;
            } else {
                $response['message'] = 'User not authenticated';
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$conn->close();
?>
