<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require_once 'db.php';

function handleError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}

// Handle GET requests to fetch transactions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['type']) && $_GET['type'] === 'budget') {
        // Fetch user-specific budget
        $stmt = $conn->prepare("SELECT amount FROM budget_settings WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $budget = $stmt->fetch();
        echo json_encode(['budget' => $budget['amount'] ?? 0]);
        exit;
    }
    
    // Fetch user-specific transactions
    $stmt = $conn->prepare("SELECT * FROM transactions1 WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($transactions);
    exit;
}

// Handle PUT requests to update budget
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['budget']) || !is_numeric($data['budget'])) {
        handleError('Invalid budget value');
    }
    
    $newBudget = floatval($data['budget']);
    
    // First check if a budget setting exists for the user
    $stmt = $conn->prepare("SELECT id FROM budget_settings WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing budget
        $stmt = $conn->prepare("UPDATE budget_settings SET amount = ? WHERE id = ?");
        $success = $stmt->execute([$newBudget, $existing['id']]);
    } else {
        // Insert new budget setting
        $stmt = $conn->prepare("INSERT INTO budget_settings (user_id, amount) VALUES (?, ?)");
        $success = $stmt->execute([$_SESSION['user_id'], $newBudget]);
    }
    
    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        handleError("Failed to update budget");
    }
    exit;
}

// Handle POST requests to add/edit a transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    // Validate required fields
    $required = ['icon', 'title', 'amount', 'date'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            handleError("Missing required field: $field");
        }
    }
    
    $data = [
        'user_id' => $_SESSION['user_id'],
        'icon' => $_POST['icon'],
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? '',
        'amount' => floatval($_POST['amount']),
        'date' => $_POST['date']
    ];
    
    try {
        if ($id) {
            // Update existing transaction (verify ownership)
            $stmt = $conn->prepare("UPDATE transactions1 SET icon=?, title=?, description=?, amount=?, date=? 
                                  WHERE id=? AND user_id=?");
            $success = $stmt->execute([$data['icon'], $data['title'], $data['description'], 
                                     $data['amount'], $data['date'], $id, $_SESSION['user_id']]);
        } else {
            // Insert new transaction
            $stmt = $conn->prepare("INSERT INTO transactions1 (user_id, icon, title, description, amount, date) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([$data['user_id'], $data['icon'], $data['title'], 
                                     $data['description'], $data['amount'], $data['date']]);
        }
        
        if ($success) {
            echo json_encode(["success" => true]);
        } else {
            handleError("Failed to save transaction");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }
    exit;
}

// Handle DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        handleError("Missing transaction ID");
    }
    
    $id = $_GET['id'];
    
    // Delete transaction (verify ownership)
    $stmt = $conn->prepare("DELETE FROM transactions1 WHERE id = ? AND user_id = ?");
    $success = $stmt->execute([$id, $_SESSION['user_id']]);
    
    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        handleError("Failed to delete transaction");
    }
    exit;
}

$conn->close();
?>