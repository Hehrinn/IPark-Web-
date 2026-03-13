<?php
/**
 * AdminStaffController
 * Handles staff management for admins
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/Admin.php');

class AdminStaffController {
    private $admin_model;
    private $conn;
    
    public function __construct($conn) {
        $this->admin_model = new Admin($conn);
        $this->conn = $conn;
    }
    
    /**
     * Load staff management page
     */
    public function staffList() {
        requireAdmin();
        requireAdminRole('super_admin');
        
        $admin_id = getCurrentAdminId();
        $admin = $this->admin_model->getAdminById($admin_id);
        
        $limit = $_GET['limit'] ?? 50;
        $offset = (($_GET['page'] ?? 1) - 1) * $limit;
        
        $staff = $this->admin_model->getAllAdmins($limit, $offset);
        
        // Count total staff
        $result = $this->conn->query("SELECT COUNT(*) as count FROM ipark_admins");
        $total_staff = $result->fetch_assoc()['count'];
        $total_pages = ceil($total_staff / $limit);
        $current_page = $_GET['page'] ?? 1;
        
        return compact('admin', 'staff', 'total_staff', 'total_pages', 'current_page');
    }
    
    /**
     * Create new staff
     */
    public function createStaff($username, $email, $password, $full_name, $role = 'operator') {
        requireAdmin();
        requireAdminRole('super_admin');
        
        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_message'] = ['error' => 'Invalid email format'];
            return ['success' => false];
        }
        
        if (strlen($password) < 8) {
            $_SESSION['flash_message'] = ['error' => 'Password must be at least 8 characters'];
            return ['success' => false];
        }
        
        $password_hash = hashPassword($password);
        
        if ($this->admin_model->createAdmin($username, $email, $password_hash, $full_name, $role)) {
            $_SESSION['flash_message'] = ['success' => 'Staff member created successfully'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to create staff member'];
            return ['success' => false];
        }
    }
    
    /**
     * Update staff role
     */
    public function updateStaffRole($staff_id, $role) {
        requireAdmin();
        requireAdminRole('super_admin');
        
        if ($this->admin_model->updateAdminRole($staff_id, $role)) {
            $_SESSION['flash_message'] = ['success' => 'Staff role updated'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to update staff role'];
            return ['success' => false];
        }
    }
    
    /**
     * Deactivate staff
     */
    public function deactivateStaff($staff_id) {
        requireAdmin();
        requireAdminRole('super_admin');
        
        if ($this->admin_model->deactivateAdmin($staff_id)) {
            $_SESSION['flash_message'] = ['success' => 'Staff member deactivated'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to deactivate staff member'];
            return ['success' => false];
        }
    }
}
?>
