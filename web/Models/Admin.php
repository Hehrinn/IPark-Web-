<?php
/**
 * Admin Model
 * Handles admin and staff operations
 */

class Admin {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get admin by email
     */
    public function getAdminByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, password_hash, full_name, role, is_active 
            FROM ipark_admins 
            WHERE email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($admin_id) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, full_name, role, is_active, created_at 
            FROM ipark_admins 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get all admins
     */
    public function getAllAdmins($limit = 100, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT id, username, email, full_name, role, is_active, created_at 
            FROM ipark_admins 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Create admin
     */
    public function createAdmin($username, $email, $password_hash, $full_name, $role = 'admin') {
        $is_active = TRUE;
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_admins 
            (username, email, password_hash, full_name, role, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $username, $email, $password_hash, $full_name, $role, $is_active);
        return $stmt->execute();
    }
    
    /**
     * Update admin role
     */
    public function updateAdminRole($admin_id, $role) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_admins 
            SET role = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $role, $admin_id);
        return $stmt->execute();
    }
    
    /**
     * Deactivate admin
     */
    public function deactivateAdmin($admin_id) {
        $is_active = FALSE;
        $stmt = $this->conn->prepare("
            UPDATE ipark_admins 
            SET is_active = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $is_active, $admin_id);
        return $stmt->execute();
    }
    
    /**
     * Get staff (operator role)
     */
    public function getStaffOperators() {
        $role = 'operator';
        $result = $this->conn->query("
            SELECT id, username, email, full_name, role, is_active 
            FROM ipark_admins 
            WHERE role = '$role' AND is_active = TRUE
            ORDER BY full_name
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
