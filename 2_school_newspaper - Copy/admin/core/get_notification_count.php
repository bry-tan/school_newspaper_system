<?php
require_once '../classloader.php';

header('Content-Type: application/json');

if (!$userObj->isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit;
}

$unreadCount = $notificationObj->getUnreadCount($_SESSION['user_id']);
echo json_encode(['count' => $unreadCount]);
?>