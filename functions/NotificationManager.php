<?php
/**
 * Notification Functions
 */

class NotificationManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function sendNotification($to_user_id, $type, $title, $message, $from_user_id = null, $related_id = null, $related_type = null) {
        $to_user_id = (int)$to_user_id;
        $from_user_id = $from_user_id ? (int)$from_user_id : 'NULL';
        $type = $this->db->escape($type);
        $title = $this->db->escape($title);
        $message = $this->db->escape($message);
        $related_id = $related_id ? (int)$related_id : 'NULL';
        $related_type = $related_type ? $this->db->escape($related_type) : 'NULL';

        $sql = "INSERT INTO notifications (from_user_id, to_user_id, type, title, message, related_id, related_type, created_at)
                VALUES ($from_user_id, $to_user_id, '$type', '$title', '$message', $related_id, $related_type, NOW())";

        return $this->db->query($sql);
    }

    public function getUserNotifications($user_id, $limit = 20, $offset = 0) {
        $user_id = (int)$user_id;
        $sql = "SELECT n.*, u.name as from_user_name FROM notifications n 
                LEFT JOIN users u ON n.from_user_id = u.id 
                WHERE n.to_user_id = $user_id 
                ORDER BY n.created_at DESC 
                LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function getUnreadCount($user_id) {
        $user_id = (int)$user_id;
        $result = $this->db->query("SELECT COUNT(*) as unread FROM notifications WHERE to_user_id = $user_id AND is_read = 0");
        $row = $result->fetch_assoc();
        return $row['unread'] ?? 0;
    }

    public function markAsRead($notification_id) {
        $notification_id = (int)$notification_id;
        return $this->db->query("UPDATE notifications SET is_read = 1 WHERE id = $notification_id");
    }

    public function markAllAsRead($user_id) {
        $user_id = (int)$user_id;
        return $this->db->query("UPDATE notifications SET is_read = 1 WHERE to_user_id = $user_id");
    }

    public function deleteNotification($notification_id) {
        $notification_id = (int)$notification_id;
        return $this->db->query("DELETE FROM notifications WHERE id = $notification_id");
    }
}
?>