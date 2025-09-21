<?php  

require_once 'Database.php';

/**
 * Class for handling Notification-related operations.
 */
class Notification extends Database {
    
    /**
     * Creates a new notification
     * @param int $user_id The user to notify
     * @param string $type Type of notification
     * @param string $title Notification title
     * @param string $message Notification message
     * @param int|null $related_article_id Related article ID
     * @param int|null $related_user_id Related user ID
     * @return bool Success status
     */
    public function createNotification($user_id, $type, $title, $message, $related_article_id = null, $related_user_id = null) {
        $sql = "INSERT INTO notifications (user_id, type, title, message, related_article_id, related_user_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->executeNonQuery($sql, [$user_id, $type, $title, $message, $related_article_id, $related_user_id]);
    }
    
    /**
     * Gets notifications for a user
     * @param int $user_id User ID
     * @param bool $unread_only Get only unread notifications
     * @return array Notifications
     */
    public function getUserNotifications($user_id, $unread_only = false) {
        $sql = "SELECT n.*, a.title as article_title, u.username as related_username 
                FROM notifications n 
                LEFT JOIN articles a ON n.related_article_id = a.article_id 
                LEFT JOIN school_publication_users u ON n.related_user_id = u.user_id 
                WHERE n.user_id = ?";
        
        if ($unread_only) {
            $sql .= " AND n.is_read = 0";
        }
        
        $sql .= " ORDER BY n.created_at DESC";
        
        return $this->executeQuery($sql, [$user_id]);
    }
    
    /**
     * Marks a notification as read
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (for security)
     * @return bool Success status
     */
    public function markAsRead($notification_id, $user_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?";
        return $this->executeNonQuery($sql, [$notification_id, $user_id]);
    }
    
    /**
     * Gets unread notification count
     * @param int $user_id User ID
     * @return int Count of unread notifications
     */
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
        $result = $this->executeQuerySingle($sql, [$user_id]);
        return $result ? $result['count'] : 0;
    }
}

?>