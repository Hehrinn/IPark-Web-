<?php
/**
 * StaffDashboardController
 * Handles staff operator dashboard
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/Admin.php');
require_once(__DIR__ . '/../Models/StaffApproval.php');
require_once(__DIR__ . '/../Models/Report.php');

class StaffDashboardController {
    private $admin_model;
    private $approval_model;
    private $report_model;
    
    public function __construct($conn) {
        $this->admin_model = new Admin($conn);
        $this->approval_model = new StaffApproval($conn);
        $this->report_model = new Report($conn);
    }
    
    /**
     * Load staff dashboard
     */
    public function dashboard() {
        // Require staff role (operator)
        requireAdmin();
        requireAdminRole('operator');
        
        $staff_id = getCurrentAdminId();
        $staff = $this->admin_model->getAdminById($staff_id);
        
        $pending_approvals = $this->approval_model->getStaffApprovals($staff_id);
        $pending_count = $this->approval_model->getPendingApprovalsCount($staff_id);
        $kpis = $this->report_model->getDashboardKPIs();
        
        return compact('staff', 'pending_approvals', 'pending_count', 'kpis');
    }
}
?>
