<?php
/**
 * Parking Model
 * Handles parking slot and occupancy operations
 */

class Parking {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get all available parking slots
     */
    public function getAvailableSlots() {
        $status = 'available';
        $stmt = $this->conn->prepare("
            SELECT id, slot_number, floor_level, parking_lot, 
                   vehicle_type, status, hourly_rate 
            FROM ipark_parking_slots 
            WHERE status = ? 
            ORDER BY parking_lot, floor_level, slot_number
        ");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get slot by ID
     */
    public function getSlotById($slot_id) {
        $stmt = $this->conn->prepare("
            SELECT id, slot_number, floor_level, parking_lot, 
                   vehicle_type, status, hourly_rate 
            FROM ipark_parking_slots 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $slot_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get all parking slots
     */
    public function getAllSlots() {
        $result = $this->conn->query("
            SELECT id, slot_number, floor_level, parking_lot, 
                   vehicle_type, status, hourly_rate 
            FROM ipark_parking_slots 
            ORDER BY parking_lot, floor_level, slot_number
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get occupancy statistics
     */
    public function getOccupancyStats() {
        $result = $this->conn->query("
            SELECT 
                COUNT(*) as total_slots,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_slots,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_slots,
                ROUND(
                    (SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2
                ) as occupancy_percentage
            FROM ipark_parking_slots
        ");
        return $result->fetch_assoc();
    }
    
    /**
     * Get occupancy by location
     */
    public function getOccupancyByLocation() {
        $result = $this->conn->query("
            SELECT 
                parking_lot,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied,
                ROUND(
                    (SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2
                ) as percentage
            FROM ipark_parking_slots 
            GROUP BY parking_lot
            ORDER BY parking_lot
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Update slot status
     */
    public function updateSlotStatus($slot_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE ipark_parking_slots 
            SET status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $slot_id);
        return $stmt->execute();
    }
    
    /**
     * Create parking slot
     */
    public function createSlot($slot_number, $floor_level, $parking_lot, $vehicle_type, $hourly_rate) {
        $status = 'available';
        $stmt = $this->conn->prepare("
            INSERT INTO ipark_parking_slots 
            (slot_number, floor_level, parking_lot, vehicle_type, status, hourly_rate) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssd", $slot_number, $floor_level, $parking_lot, $vehicle_type, $hourly_rate);
        return $stmt->execute();
    }
    
    /**
     * Check if slot number exists
     */
    public function slotExists($slot_number) {
        $stmt = $this->conn->prepare("SELECT id FROM ipark_parking_slots WHERE slot_number = ?");
        $stmt->bind_param("s", $slot_number);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
?>
