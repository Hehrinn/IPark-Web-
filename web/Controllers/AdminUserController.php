<?php
/**
 * AdminUserController
 * Handles user management for admins
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Admin.php');

class AdminUserController {
    private $user_model;
    private $admin_model;
    
    public function __construct($conn) {
        $this->user_model = new User($conn);
        $this->admin_model = new Admin($conn);
    }
    
    /**
     * Load user management page
     */
    public function userList() {
        requireAdmin();
        requireAdminRole('super_admin', 'admin');
        
        $admin_id = getCurrentAdminId();
        $admin = $this->admin_model->getAdminById($admin_id);
        
        $limit = $_GET['limit'] ?? 50;
        $offset = (($_GET['page'] ?? 1) - 1) * $limit;
        
        $users = $this->user_model->getAllUsers($limit, $offset);
        $total_users = $this->user_model->getTotalUserCount();
        $total_pages = ceil($total_users / $limit);
        $current_page = $_GET['page'] ?? 1;
        
        return compact('admin', 'users', 'total_users', 'total_pages', 'current_page');
    }
    
    /**
     * Get user details
     */
    public function userDetail($user_id) {
        requireAdmin();
        requireAdminRole('super_admin', 'admin');
        
        $user = $this->user_model->getUserById($user_id);
        
        if (!$user) {
            $_SESSION['flash_message'] = ['error' => 'User not found'];
            return ['success' => false];
        }
        
        return $user;
    }
    
    /**
     * Deactivate user
     */
    public function deactivateUser($user_id) {
        requireAdmin();
        requireAdminRole('super_admin', 'admin');
        
        if ($this->user_model->deactivateUser($user_id)) {
            $_SESSION['flash_message'] = ['success' => 'User deactivated'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to deactivate user'];
            return ['success' => false];
        }
    }
}
?>
