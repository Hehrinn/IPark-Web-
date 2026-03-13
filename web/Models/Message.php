<?php
/**
 * Message Model
 * Handles communication between users and admins
 */

class Message {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Create message
     */
    public function createMessage($from_user_id, $from_admin_id, $to_user_id, $to_admin_id, $subject, $content, $sender_type) {
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_messages 
            (from_user_id, from_admin_id, to_user_id, to_admin_id, subject, content, sender_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiiiss", $from_user_id, $from_admin_id, $to_user_id, $to_admin_id, $subject, $content, $sender_type);
        return $stmt->execute();
    }
    
    /**
     * Get messages for user
     */
    public function getUserMessages($user_id, $limit = 50) {
        $stmt = $this->conn->prepare("
            SELECT m.id, m.subject, m.content, m.sender_type, m.is_read, m.created_at,
                   a.full_name as admin_name
            FROM ipark_messages m
            LEFT JOIN ipark_admins a ON m.from_admin_id = a.id
            WHERE m.to_user_id = ?
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get messages for admin
     */
    public function getAdminMessages($admin_id, $limit = 50) {
        $stmt = $this->conn->prepare("
            SELECT m.id, m.subject, m.content, m.sender_type, m.is_read, m.created_at,
                   u.full_name as user_name, u.email
            FROM ipark_messages m
            LEFT JOIN ipark_users u ON m.from_user_id = u.id
            WHERE m.to_admin_id = ?
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $admin_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead($message_id) {
        $is_read = TRUE;
        $stmt = $this->conn->prepare("
            UPDATE ipark_messages 
            SET is_read = ?, read_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $is_read, $message_id);
        return $stmt->execute();
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount($user_id = null, $admin_id = null) {
        if ($user_id) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM ipark_messages WHERE to_user_id = ? AND is_read = FALSE");
            $stmt->bind_param("i", $user_id);
        } else {
            if (!$admin_id) return 0; // Avoid query if no id
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM ipark_messages WHERE to_admin_id = ? AND is_read = FALSE");
            $stmt->bind_param("i", $admin_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] ?? 0;
    }
    
    /**
     * Delete message
     */
    public function deleteMessage($message_id) {
        $stmt = $this->conn->prepare("DELETE FROM ipark_messages WHERE id = ?");
        $stmt->bind_param("i", $message_id);
        return $stmt->execute();
    }
}
?>
