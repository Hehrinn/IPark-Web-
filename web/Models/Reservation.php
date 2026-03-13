<?php
/**
 * Reservation Model
 * Handles reservation and approval operations
 */

class Reservation {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Create reservation
     */
    public function createReservation($user_id, $parking_slot_id, $start_time, $end_time, $total_amount) {
        $reservation_status = 'pending_approval';
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_reservations 
            (user_id, parking_slot_id, check_in_time, check_out_time, total_amount, reservation_status, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iissds", $user_id, $parking_slot_id, $start_time, $end_time, $total_amount, $reservation_status);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Get user reservations
     */
    public function getUserReservations($user_id, $limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT r.id, r.user_id, r.parking_slot_id, r.check_in_time as start_time, r.check_out_time as end_time, 
                   r.total_amount, r.reservation_status, r.payment_status, r.created_at,
                   s.slot_number, s.parking_lot
            FROM ipark_reservations r
            JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get reservation by ID
     */
    public function getReservationById($reservation_id) {
        $stmt = $this->conn->prepare("
            SELECT r.id, r.user_id, r.parking_slot_id, r.check_in_time as start_time, r.check_out_time as end_time, 
                   r.total_amount, r.reservation_status, r.payment_status, r.created_at, s.slot_number, s.parking_lot, s.hourly_rate,
                   u.full_name, u.email
            FROM ipark_reservations r
            JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
            JOIN ipark_users u ON r.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get all reservations (for admin)
     */
    public function getAllReservations($status = null, $limit = 50, $offset = 0) {
        $sql = "
            SELECT r.id, r.user_id, r.parking_slot_id, r.check_in_time as start_time, r.check_out_time as end_time, 
                   r.total_amount, r.reservation_status, r.payment_status, r.created_at,
                   s.slot_number, s.parking_lot,
                   u.full_name, u.email
            FROM ipark_reservations r
            JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
            JOIN ipark_users u ON r.user_id = u.id
        ";
        if ($status) {
            $sql .= " WHERE r.reservation_status = ?";
            $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $status, $limit, $offset);
        } else {
            $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get pending reservations
     */
    public function getPendingReservations() {
        $status = 'pending_approval';
        $stmt = $this->conn->prepare("
            SELECT r.id, r.check_in_time as start_time, r.check_out_time as end_time, r.total_amount, r.created_at,
                   s.slot_number, s.parking_lot,
                   u.full_name, u.email
            FROM ipark_reservations r
            JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
            JOIN ipark_users u ON r.user_id = u.id
            WHERE r.reservation_status = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get active reservation for a user and slot
     */
    public function getActiveReservation($user_id, $slot_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM ipark_reservations 
            WHERE user_id = ? AND parking_slot_id = ? 
            AND reservation_status IN ('pending_approval', 'approved', 'active')
            LIMIT 1
        ");
        $stmt->bind_param("ii", $user_id, $slot_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update reservation status
     */
    public function updateReservationStatus($reservation_id, $status, $admin_id = null) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_reservations 
            SET reservation_status = ?, admin_approved_by = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("sii", $status, $admin_id, $reservation_id);
        return $stmt->execute();
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($reservation_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_reservations 
            SET payment_status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $reservation_id);
        return $stmt->execute();
    }
    
    /**
     * Cancel reservation
     */
    public function cancelReservation($reservation_id) {
        $status = 'cancelled';
        $stmt = $this->conn->prepare("
            UPDATE ipark_reservations 
            SET reservation_status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $reservation_id);
        return $stmt->execute();
    }
    
    /**
     * Update reservation details (Re-book)
     */
    public function updateReservationDetails($reservation_id, $user_id, $parking_slot_id, $start_time, $end_time, $total_amount) {
        // Reset status to pending_approval on update so admin can review again
        $status = 'pending_approval';
        $stmt = $this->conn->prepare("
            UPDATE ipark_reservations 
            SET parking_slot_id = ?, check_in_time = ?, check_out_time = ?, total_amount = ?, reservation_status = ?, updated_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("issdsii", $parking_slot_id, $start_time, $end_time, $total_amount, $status, $reservation_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Delete reservation (Permanently remove)
     */
    public function deleteReservation($reservation_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM ipark_reservations WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $reservation_id, $user_id);
        return $stmt->execute();
    }
    
    /**
     * Get today's reservations count
     */
    public function getTodayReservationsCount() {
        $result = $this->conn->query("
            SELECT COUNT(*) as count 
            FROM ipark_reservations 
            WHERE DATE(created_at) = CURDATE()
        ");
        return $result->fetch_assoc()['count'];
    }
    
    /**
     * Get total reservations
     */
    public function getTotalReservationsCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM ipark_reservations");
        return $result->fetch_assoc()['count'];
    }
}
?>
