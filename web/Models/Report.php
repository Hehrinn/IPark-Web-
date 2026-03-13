<?php
/**
 * Report Model
 * Handles analytics and reporting operations
 */

class Report {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get KPI dashboard data
     */
    public function getDashboardKPIs() {
        $result = $this->conn->query("
            SELECT 
                (SELECT COUNT(*) FROM ipark_reservations WHERE DATE(created_at) = CURDATE()) as today_reservations,
                (SELECT SUM(total_amount) FROM ipark_reservations WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid') as today_revenue,
                (SELECT COUNT(*) FROM ipark_users WHERE is_active = TRUE) as active_users,
                (SELECT COUNT(*) FROM ipark_reservations WHERE reservation_status = 'pending_approval') as pending_approvals,
                (SELECT COUNT(*) FROM ipark_parking_slots WHERE status = 'occupied') as occupied_slots,
                (SELECT COUNT(*) FROM ipark_parking_slots) as total_slots
        ");
        return $result->fetch_assoc();
    }
    
    /**
     * Get revenue report
     */
    public function getRevenueReport($start_date = null, $end_date = null) {
        if (!$start_date) {
            $start_date = date('Y-m-01'); // First day of month
        }
        if (!$end_date) {
            $end_date = date('Y-m-d'); // Today
        }
        
        $stmt = $this->conn->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as transactions,
                SUM(total_amount) as revenue,
                AVG(total_amount) as avg_amount
            FROM ipark_reservations 
            WHERE DATE(created_at) BETWEEN ? AND ? AND payment_status = 'paid'
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get occupancy trends
     */
    public function getOccupancyTrends($days = 30) {
        $result = $this->conn->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(CASE WHEN status = 'occupied' THEN 1 END) as occupied,
                COUNT(*) as total,
                ROUND(COUNT(CASE WHEN status = 'occupied' THEN 1 END) / COUNT(*) * 100, 2) as percentage
            FROM ipark_parking_slots
            GROUP BY DATE(created_at)
            ORDER BY date DESC
            LIMIT $days
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get top parking lots
     */
    public function getTopParkingLots() {
        $result = $this->conn->query("
            SELECT 
                s.parking_lot,
                COUNT(r.id) as reservations,
                SUM(r.total_amount) as revenue,
                COUNT(CASE WHEN s.status = 'occupied' THEN 1 END) as occupied
            FROM ipark_parking_slots s
            LEFT JOIN ipark_reservations r ON s.id = r.parking_slot_id
            GROUP BY s.parking_lot
            ORDER BY reservations DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get user activity report
     */
    public function getUserActivityReport($limit = 100) {
        $stmt = $this->conn->prepare("
            SELECT 
                u.id, u.full_name, u.email,
                COUNT(r.id) as total_reservations,
                SUM(r.total_amount) as total_spent,
                MAX(r.created_at) as last_reservation
            FROM ipark_users u
            LEFT JOIN ipark_reservations r ON u.id = r.user_id
            GROUP BY u.id
            ORDER BY total_spent DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue($start_date = null, $end_date = null) {
        if ($start_date && $end_date) {
            $stmt = $this->conn->prepare("
                SELECT SUM(total_amount) as revenue 
                FROM ipark_reservations 
                WHERE DATE(created_at) BETWEEN ? AND ? AND payment_status = 'paid'
            ");
            $stmt->bind_param("ss", $start_date, $end_date);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['revenue'] ?? 0;
        } else {
            $result = $this->conn->query("
                SELECT SUM(total_amount) as revenue 
                FROM ipark_reservations 
                WHERE payment_status = 'paid'
            ");
            return $result->fetch_assoc()['revenue'] ?? 0;
        }
    }
    
    /**
     * Get average reservation value
     */
    public function getAverageReservationValue() {
        $result = $this->conn->query("
            SELECT AVG(total_amount) as average 
            FROM ipark_reservations 
            WHERE payment_status = 'paid'
        ");
        return $result->fetch_assoc()['average'] ?? 0;
    }
}
?>
