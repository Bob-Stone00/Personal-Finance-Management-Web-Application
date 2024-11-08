<?php
// notifications.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require_once 'db.php';

function createNotification($userId, $transactionId, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, transaction_id, message, created_at, is_read) 
                           VALUES (?, ?, ?, NOW(), 0)");
    return $stmt->execute([$userId, $transactionId, $message]);
}

// Get notifications for the current user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lastChecked = isset($_SESSION['last_notification_check']) 
        ? $_SESSION['last_notification_check'] 
        : date('Y-m-d H:i:s', strtotime('-1 day'));

    $stmt = $conn->prepare("SELECT n.*, t.title, t.amount 
                           FROM notifications n
                           LEFT JOIN transactions1 t ON n.transaction_id = t.id
                           WHERE n.user_id = ? AND n.created_at > ?
                           ORDER BY n.created_at DESC
                           LIMIT 10");
    
    $stmt->execute([$_SESSION['user_id'], $lastChecked]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update last checked timestamp
    $_SESSION['last_notification_check'] = date('Y-m-d H:i:s');

    // Mark notifications as read
    if (!empty($notifications)) {
        $stmt = $conn->prepare("UPDATE notifications 
                               SET is_read = 1 
                               WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$_SESSION['user_id']]);
    }

    echo json_encode([
        'notifications' => $notifications,
        'count' => count($notifications)
    ]);
    exit();
}
?>