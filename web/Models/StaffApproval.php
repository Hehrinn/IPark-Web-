<?php
/**
 * StaffApproval Model
 * Handles staff approval operations
 */

class StaffApproval {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Create approval request
     */
    public function createApproval($reservation_id, $staff_id, $notes = '') {
        $approval_status = 'pending';
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_staff_approvals 
            (reservation_id, staff_id, approval_status, notes) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiss", $reservation_id, $staff_id, $approval_status, $notes);
        return $stmt->execute();
    }
    
    /**
     * Get approval requests for staff member
     */
    public function getStaffApprovals($staff_id) {
        $stmt = $this->conn->prepare("
            SELECT sa.id, sa.reservation_id, sa.approval_status, sa.notes, sa.created_at,
                   r.check_in_time as start_time, r.check_out_time as end_time, r.total_amount,
                   s.slot_number, s.parking_lot,
                   u.full_name, u.email
            FROM ipark_staff_approvals sa
            JOIN ipark_reservations r ON sa.reservation_id = r.id
            JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
            JOIN ipark_users u ON r.user_id = u.id
            WHERE sa.staff_id = ? AND sa.approval_status = 'pending'
            ORDER BY sa.created_at DESC
        ");
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get pending approvals count
     */
    public function getPendingApprovalsCount($staff_id) {
        $status = 'pending';
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as count 
            FROM ipark_staff_approvals 
            WHERE staff_id = ? AND approval_status = ?
        ");
        $stmt->bind_param("is", $staff_id, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    /**
     * Approve reservation
     */
    public function approveReservation($approval_id, $staff_id) {
        $approval_status = 'approved';
        $stmt = $this->conn->prepare("
            UPDATE ipark_staff_approvals 
            SET approval_status = ?, approved_by_staff_id = ?, approved_at = NOW() 
            WHERE id = ? AND staff_id = ?
        ");
        $stmt->bind_param("siii", $approval_status, $staff_id, $approval_id, $staff_id);
        return $stmt->execute();
    }
    
    /**
     * Reject reservation
     */
    public function rejectReservation($approval_id, $staff_id, $rejection_reason) {
        $approval_status = 'rejected';
        $stmt = $this->conn->prepare("
            UPDATE ipark_staff_approvals 
            SET approval_status = ?, rejection_reason = ?, rejected_at = NOW() 
            WHERE id = ? AND staff_id = ?
        ");
        $stmt->bind_param("ssii", $approval_status, $rejection_reason, $approval_id, $staff_id);
        return $stmt->execute();
    }
    
    /**
     * Get all approvals (for admin)
     */
    public function getAllApprovals($status = null) {
        $sql = "
            SELECT sa.*, 
                   r.check_in_time as start_time, r.check_out_time as end_time, 
                   admin.full_name as staff_name
            FROM ipark_staff_approvals sa
            JOIN ipark_reservations r ON sa.reservation_id = r.id
            JOIN ipark_admins admin ON sa.staff_id = admin.id
        ";
        if ($status) {
            $sql .= " WHERE sa.approval_status = ? ORDER BY sa.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
        } else {
            $sql .= " ORDER BY sa.created_at DESC";
            $stmt = $this->conn->prepare($sql);
        }

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>
