<?php
/**
 * User Model
 * Handles user-related database operations
 */

class User {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, password_hash, full_name, phone, 
                   email_verified_at, is_active 
            FROM ipark_users 
            WHERE email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, full_name, phone, 
                   email_verified_at, is_active, created_at 
            FROM ipark_users 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Create new user
     */
    public function createUser($username, $email, $password_hash, $full_name, $phone, $verification_token) {
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_users 
            (username, email, password_hash, full_name, phone, verification_token, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, FALSE)
        ");
        $stmt->bind_param("ssssss", $username, $email, $password_hash, $full_name, $phone, $verification_token);
        return $stmt->execute();
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM ipark_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT id FROM ipark_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Update user profile
     */
    public function updateUser($user_id, $full_name, $phone) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_users 
            SET full_name = ?, phone = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $full_name, $phone, $user_id);
        return $stmt->execute();
    }
    
    /**
     * Update user password
     */
    public function updatePassword($user_id, $password_hash) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_users 
            SET password_hash = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $password_hash, $user_id);
        return $stmt->execute();
    }
    
    /**
     * Verify email
     */
    public function verifyEmail($email, $token) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_users 
            SET email_verified_at = NOW(), is_active = TRUE 
            WHERE email = ? AND verification_token = ?
        ");
        $stmt->bind_param("ss", $email, $token);
        return $stmt->execute();
    }
    
    /**
     * Get all users (for admin)
     */
    public function getAllUsers($limit = 100, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, full_name, phone, 
                   is_active, email_verified_at, created_at 
            FROM ipark_users 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get total user count
     */
    public function getTotalUserCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM ipark_users");
        return $result->fetch_assoc()['count'];
    }
    
    /**
     * Deactivate user
     */
    public function deactivateUser($user_id) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_users 
            SET is_active = FALSE, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>
