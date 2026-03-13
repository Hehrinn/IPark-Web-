<?php
/**
 * StaffApprovalController
 * Handles staff approval workflow
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/StaffApproval.php');
require_once(__DIR__ . '/../Models/Reservation.php');
require_once(__DIR__ . '/../Models/Admin.php');

class StaffApprovalController {
    private $approval_model;
    private $reservation_model;
    private $admin_model;
    
    public function __construct($conn) {
        $this->approval_model = new StaffApproval($conn);
        $this->reservation_model = new Reservation($conn);
        $this->admin_model = new Admin($conn);
    }
    
    /**
     * Load approvals page
     */
    public function approvals() {
        requireAdmin();
        requireAdminRole('operator');
        
        $staff_id = getCurrentAdminId();
        $staff = $this->admin_model->getAdminById($staff_id);
        
        $pending = $this->approval_model->getStaffApprovals($staff_id);
        $pending_count = count($pending);
        
        return compact('staff', 'pending', 'pending_count');
    }
    
    /**
     * Approve reservation
     */
    public function approve($approval_id) {
        requireAdmin();
        requireAdminRole('operator');
        
        $staff_id = getCurrentAdminId();
        
        // Verify approval belongs to this staff member
        $approvals = $this->approval_model->getStaffApprovals($staff_id);
        $approval_found = false;
        $reservation_id = null;
        
        foreach ($approvals as $approval) {
            if ($approval['id'] == $approval_id) {
                $approval_found = true;
                $reservation_id = $approval['reservation_id'];
                break;
            }
        }
        
        if (!$approval_found) {
            $_SESSION['flash_message'] = ['error' => 'Approval not found'];
            return ['success' => false];
        }
        
        // Approve
        if ($this->approval_model->approveReservation($approval_id, $staff_id)) {
            // Update reservation status
            $this->reservation_model->updateReservationStatus($reservation_id, 'approved', $staff_id);
            
            logActivity('reservation_approved', 'ipark_reservations', $reservation_id, $staff_id);
            
            $_SESSION['flash_message'] = ['success' => 'Reservation approved'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to approve reservation'];
            return ['success' => false];
        }
    }
    
    /**
     * Reject reservation
     */
    public function reject($approval_id, $reason) {
        requireAdmin();
        requireAdminRole('operator');
        
        $staff_id = getCurrentAdminId();
        
        // Verify approval belongs to this staff member
        $approvals = $this->approval_model->getStaffApprovals($staff_id);
        $approval_found = false;
        $reservation_id = null;
        
        foreach ($approvals as $approval) {
            if ($approval['id'] == $approval_id) {
                $approval_found = true;
                $reservation_id = $approval['reservation_id'];
                break;
            }
        }
        
        if (!$approval_found) {
            $_SESSION['flash_message'] = ['error' => 'Approval not found'];
            return ['success' => false];
        }
        
        // Reject
        if ($this->approval_model->rejectReservation($approval_id, $staff_id, $reason)) {
            // Update reservation status
            $this->reservation_model->updateReservationStatus($reservation_id, 'rejected', $staff_id);
            
            logActivity('reservation_rejected', 'ipark_reservations', $reservation_id, $staff_id);
            
            $_SESSION['flash_message'] = ['success' => 'Reservation rejected'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to reject reservation'];
            return ['success' => false];
        }
    }
}
?>
